<?php

/**
 * @project:   Simple Live Chat
 *
 * @author     Fabian Bitter (fabian@bitter.de)
 * @copyright  (C) 2020 Fabian Bitter (www.bitter.de)
 * @version    X.X.X
 */

namespace Bitter\SimpleLiveChat\Validator;

use Concrete\Core\Page\Page;
use Concrete\Core\Validator\AbstractTranslatableValidator;
use ArrayAccess;
use InvalidArgumentException;

class PageValidator extends AbstractTranslatableValidator
{
    public function isValid($mixed, ArrayAccess $error = null)
    {
        if ($mixed !== null && !is_string($mixed)) {
            throw new InvalidArgumentException(t('Invalid type supplied to validator.'));
        }

        $page = Page::getByID($mixed);

        if (!$page instanceof Page) {
            $error[] = t('The given page is not valid.');
            return false;
        } else {
            return true;
        }
    }
}