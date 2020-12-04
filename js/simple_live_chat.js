/**
 * @project:   Simple Live Chat
 *
 * @author     Fabian Bitter (fabian@bitter.de)
 * @copyright  (C) 2020 Fabian Bitter (www.bitter.de)
 * @version    X.X.X
 */

(function ($) {
    $.fn.simpleLiveChat = function () {
        let config = {};
        let defaultConfig = {
            port: 8080,
            messages: {
                serverOffline: '',
                loadingMessages: '',
                noMessages: ''
            }
        };

        let $messageContainer = this;
        let $messagesContainer = $messageContainer.find(".slc-messages-container");
        let $sidebarContainer = $messageContainer.find(".slc-sidebar-container");
        let $messageText = $messageContainer.find(".slc-actions-container .slc-message-text");
        let $sendButton = $messageContainer.find(".slc-actions-container .slc-send-message-button");
        let messageTemplate = $("#" + $messageContainer.data("messageTemplateId")).html();
        let visitorTemplate = $("#" + $messageContainer.data("visitorTemplateId")).html();
        let refreshTimestampsInterval = 15000;
        let scrollToBottomAnimationTime = 1000;
        let refreshTimestampsTimer, connection, pushToken = null;
        let isAdminMode = (typeof SLC_ADMIN_SECRET !== "undefined");

        let refreshTimestamps = function () {
            $messageContainer.find(".slc-message-time").each(function () {
                let messageTimestamp = $(this).data("timestamp");
                let messageDate = new Date(messageTimestamp);
                let messageRelativeTime = moment(messageDate).startOf("minute").fromNow();
                $(this).html(messageRelativeTime);
            });
        };

        let setCookie = function (name, value, days) {
            let d = new Date();
            d.setTime(d.getTime() + (days * 24 * 60 * 60 * 1000));
            let expires = "expires=" + d.toUTCString();
            window.document.cookie = name + "=" + value + ";" + expires + ";path=/";
        };

        let getCookie = function (name) {
            let decodedCookie = decodeURIComponent(window.document.cookie);
            let ca = decodedCookie.split(';');

            name += "=";

            for (let i = 0; i < ca.length; i++) {
                let c = ca[i];

                while (c.charAt(0) === ' ') {
                    c = c.substring(1);
                }

                if (c.indexOf(name) === 0) {
                    return c.substring(name.length, c.length);
                }
            }

            return "";
        };

        let getClientId = function () {
            if (isAdminMode) {
                return 'admin';
            } else {
                let clientId = getCookie("clientId");

                if (clientId === "") {
                    // RFC4122: The version 4 UUID is meant for generating UUIDs from truly-random or
                    // pseudo-random numbers.
                    // The algorithm is as follows:
                    //     Set the two most significant bits (bits 6 and 7) of the
                    //        clock_seq_hi_and_reserved to zero and one, respectively.
                    //     Set the four most significant bits (bits 12 through 15) of the
                    //        time_hi_and_version field to the 4-bit version number from
                    //        Section 4.1.3. Version4
                    //     Set all the other bits to randomly (or pseudo-randomly) chosen
                    //     values.
                    // UUID                   = time-low "-" time-mid "-"time-high-and-version "-"clock-seq-reserved and low(2hexOctet)"-" node
                    // time-low               = 4hexOctet
                    // time-mid               = 2hexOctet
                    // time-high-and-version  = 2hexOctet
                    // clock-seq-and-reserved = hexOctet:
                    // clock-seq-low          = hexOctet
                    // node                   = 6hexOctet
                    // Format: xxxxxxxx-xxxx-4xxx-yxxx-xxxxxxxxxxxx
                    // y could be 1000, 1001, 1010, 1011 since most significant two bits needs to be 10
                    // y values are 8, 9, A, B

                    let guidHolder = 'xxxxxxxx-xxxx-4xxx-yxxx-xxxxxxxxxxxx';
                    let hex = '0123456789abcdef';
                    let r = 0;

                    clientId = '';

                    for (let i = 0; i < 36; i++) {
                        if (guidHolder[i] !== '-' && guidHolder[i] !== '4') {
                            // each x and y needs to be random
                            r = Math.random() * 16 | 0;
                        }

                        if (guidHolder[i] === 'x') {
                            clientId += hex[r];
                        } else if (guidHolder[i] === 'y') {
                            // clock-seq-and-reserved first hex is filtered and remaining hex values are random
                            r &= 0x3; // bit and with 0011 to set pos 2 to zero ?0??
                            r |= 0x8; // set pos 3 to 1 as 1???
                            clientId += hex[r];
                        } else {
                            clientId += guidHolder[i];
                        }
                    }

                    setCookie("clientId", clientId, 365);
                }

                return clientId;
            }
        };

        let isChatActive = function (clientId) {
            if (isAdminMode) {
                let $visitorEl = $sidebarContainer.find(".slc-visitor[data-client-id='" + clientId + "']");
                return ($visitorEl.length === 1 && $visitorEl.hasClass("active"));
            } else {
                return true;
            }
        };

        let addMessage = function (message) {
            if (isChatActive(message.to) || isChatActive(message.from)) {
                let messageHtml = _.template(messageTemplate, {
                    message: message,
                    config: config,
                    clientId: getClientId()
                });

                $messagesContainer
                    .append(messageHtml)
                    .stop()
                    .animate({
                        scrollTop: $messagesContainer[0].scrollHeight
                    }, scrollToBottomAnimationTime);

                refreshTimestamps();
            } else if (isAdminMode) {
                let $visitorEl = $sidebarContainer.find(".slc-visitor[data-client-id='" + message.from + "']");

                let $messageCounter = $visitorEl.find(".slc-unread-messages-counter");
                let unreadMessages = parseInt($messageCounter.data("unreadMessages"));

                if (isNaN(unreadMessages)) {
                    unreadMessages = 0;
                }

                unreadMessages++;

                $messageCounter
                    .data("unreadMessages", unreadMessages)
                    .html(unreadMessages)
                    .removeClass("slc-hidden");

                sortVisitors();
            }
        };

        let loadMessages = function (messages) {
            if (typeof messages === "object" && messages.length) {
                let firstMessage = messages[Object.keys(messages)[0]];

                if (isChatActive(firstMessage.to) || isChatActive(firstMessage.from)) {
                    $messagesContainer.html("");

                    for (let message of messages) {
                        addMessage(message);
                    }
                }
            } else if (isAdminMode) {
                $messagesContainer.html(config.messages.noMessages);
            }
        };

        let selectVisitor = function (clientId) {
            if (!$sidebarContainer.find(".slc-visitor[data-client-id='" + clientId + "']").hasClass("active")) {
                $sidebarContainer.find(".slc-visitor").removeClass("active");
                $sidebarContainer.find(".slc-visitor[data-client-id='" + clientId + "']").addClass("active");

                $messagesContainer.html(config.messages.loadingMessages);

                // noinspection JSIgnoredPromiseFromCall
                sendCommand('getMessages', {
                    visitor: clientId
                });

                $messageText.trigger("focus");
            }
        };

        let loadVisitors = function (visitors) {
            for (let visitor of visitors) {
                if ($sidebarContainer.find(".slc-visitor[data-client-id='" + visitor.clientId + "']").length === 0) {
                    let visitorHtml = _.template(visitorTemplate, {
                        visitor: visitor
                    });

                    $sidebarContainer.prepend(visitorHtml)

                    // was the message send within the last 15 seconds?
                    let isNewMessage = Date.now() - 15000 < visitor.lastTimestamp;

                    if (isNewMessage) {
                        // noinspection JSStringConcatenationToES6Template
                        $sidebarContainer.find(".slc-visitor[data-client-id='" + visitor.clientId + "']")
                            .find(".slc-unread-messages-counter")
                            .data("unreadMessages", 1)
                            .html("1")
                            .removeClass("slc-hidden");
                    }

                    $sidebarContainer.find(".slc-visitor[data-client-id='" + visitor.clientId + "']").bind("click", function (e) {
                        e.preventDefault();

                        // noinspection JSStringConcatenationToES6Template
                        $(this)
                            .find(".slc-unread-messages-counter")
                            .data("unreadMessages", 0)
                            .html("")
                            .addClass("slc-hidden");

                        let clientId = $(this).data("clientId");

                        selectVisitor(clientId);

                        return false;
                    });
                } else {
                    // noinspection JSUnresolvedVariable
                    $sidebarContainer.find(".slc-visitor[data-client-id='" + visitor.clientId + "'] .slc-message-time").data("timestamp", visitor.lastTimestamp);
                }
            }

            refreshTimestamps();

            sortVisitors();

            if ($sidebarContainer.find(".slc-visitor.active").length === 0) {
                // select first visitor
                selectVisitor($sidebarContainer.find(".slc-visitor:first-child").data("clientId"));
            }
        };

        let sortVisitors = function () {

            let $temp = $("<div/>");

            $sidebarContainer
                .find('.slc-visitor')
                .sort(function (a, b) {
                    // noinspection JSStringConcatenationToES6Template
                    return parseInt($(a).find(".slc-message-time").data("timestamp")) > parseInt($(b).find(".slc-message-time").data("timestamp")) ? 1 : -1;
                })
                .each(function () {
                    $(this).prependTo($temp);
                });

            $sidebarContainer.html($temp);
        };

        let bindEventHandlers = function () {
            $messageText.bind("keyup", function (e) {
                // noinspection JSDeprecatedSymbols
                if (e.keyCode === 13) {
                    $sendButton.trigger("click");
                }
            }).bind("change keypress keydown keyup", function () {
                if ($messageText.val().trim().length) {
                    $sendButton
                        .prop("disabled", false)
                        .removeClass("disabled");
                } else {
                    $sendButton
                        .prop("disabled", true)
                        .addClass("disabled");
                }
            }).trigger("change");

            $sendButton.bind("click", function () {
                let messageText = $messageText.val();
                let message;

                if (isAdminMode) {
                    message = {
                        from: 'admin',
                        to: $sidebarContainer.find(".slc-visitor.active").data("clientId"),
                        body: messageText,
                        timestamp: new Date().getTime()
                    };
                } else {
                    message = {
                        from: getClientId(),
                        to: 'admin',
                        body: messageText,
                        timestamp: new Date().getTime()
                    };
                }

                // noinspection JSIgnoredPromiseFromCall
                sendCommand("sendMessage", message);

                addMessage(message);

                $messageText
                    .val("")
                    .trigger('focus')
                    .trigger("change");
            });

            if (refreshTimestampsTimer !== null) {
                clearInterval(refreshTimestampsTimer);
            }

            refreshTimestampsTimer = setInterval(function () {
                refreshTimestamps();
            }, refreshTimestampsInterval);
        };

        let sendCommand = async function (command, additionalData) {

            // noinspection JSUnresolvedVariable
            if (pushToken === null &&
                typeof firebase === "object" &&
                typeof firebase.messaging === "function") {

                try {
                    // get push token if push notification add-on or FCM is available
                    pushToken = await firebase.messaging().getToken();
                } catch (err) {
                    // Browser doesn't support firebase or FCM is not ready yet
                }
            }

            if (connection !== null && connection.readyState === 1) {
                let payload = {
                    command: command,
                    clientId: getClientId(),
                    pushToken: pushToken,
                    chatUrl: window.location.href,
                    secret: isAdminMode ? SLC_ADMIN_SECRET : null
                };

                if (typeof additionalData !== "undefined") {
                    payload = $.extend(payload, additionalData);
                }

                let data = JSON.stringify(payload);

                connection.send(data);
            }
        }

        let initSocket = function () {
            let socketUrl = (window.location.protocol === "https:" ? "wss" : "ws") + "://" + window.location.host + ":" + config.port;

            connection = new WebSocket(socketUrl);

            connection.onopen = function () {
                // noinspection JSIgnoredPromiseFromCall
                sendCommand('registerClient');

                if (isAdminMode) {
                    // noinspection JSIgnoredPromiseFromCall
                    sendCommand('getVisitors');

                    $messageContainer.addClass("slc-admin-mode");
                } else {
                    // noinspection JSIgnoredPromiseFromCall
                    sendCommand('getMessages');
                }

            };

            connection.onclose = function() {
                setTimeout(function() {
                    // try to re-connect
                    connection = new WebSocket(socketUrl);
                }, 1000);
            };

            connection.onerror = function () {
                // noinspection JSUnresolvedVariable
                $messagesContainer.html(config.messages.serverOffline);
                $messageText.prop("disabled", true).addClass("disabled");
                $sendButton.prop("disabled", true).addClass("disabled");
            };

            connection.onmessage = function (event) {
                let payload = JSON.parse(event.data);

                switch (payload.command) {
                    case "refreshVisitors":
                        // noinspection JSIgnoredPromiseFromCall
                        sendCommand('getVisitors');
                        break;

                    case "loadVisitors":
                        // noinspection JSUnresolvedVariable
                        loadVisitors(payload.visitors);

                        break;

                    case "loadMessages":
                        // noinspection JSUnresolvedVariable
                        loadMessages(payload.messages);
                        break;

                    case "loadMessage":
                        addMessage(payload.message);

                        break;
                }

            };
        };

        let loadSettings = function () {
            // noinspection JSUnresolvedVariable
            let url = CCM_DISPATCHER_FILENAME + '/simple_live_chat/api/get_config?_=' + new Date().getTime();

            $.getJSON(url, function (data) {
                config = $.extend(defaultConfig, data);

                if (isAdminMode) {
                    // swap profile pictures for admin user
                    let temp = config.visitorProfilePicture;
                    config.visitorProfilePicture = config.agentProfilePicture;
                    config.agentProfilePicture = temp;
                }

                bindEventHandlers();
                initSocket();
            });
        };

        loadSettings();

        return this;
    };
})(jQuery);