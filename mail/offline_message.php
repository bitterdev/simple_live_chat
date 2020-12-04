<?php

/**
 * @project:   Simple Live Chat
 *
 * @author     Fabian Bitter (fabian@bitter.de)
 * @copyright  (C) 2020 Fabian Bitter (www.bitter.de)
 * @version    X.X.X
 */

defined('C5_EXECUTE') or die("Access Denied.");

use Concrete\Core\Support\Facade\Url;

$subject = t('Offline message');

$body = t(
    "A visitor has sent you a message. Click %s to reply.",
    sprintf(
        "<a href=\"\" target=\"_blank\">%s</a>",
        (string)Url::to("/dashboard/simple_live_chat/chat"),
        t("here")
    )
);
