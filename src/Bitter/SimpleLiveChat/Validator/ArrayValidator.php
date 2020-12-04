<?php

/**
 * @project:   Simple Live Chat
 *
 * @author     Fabian Bitter (fabian@bitter.de)
 * @copyright  (C) 2020 Fabian Bitter (www.bitter.de)
 * @version    X.X.X
 */

namespace Bitter\SimpleLiveChat\Validator;

use Concrete\Core\Validator\AbstractTranslatableValidator;
use ArrayAccess;
use InvalidArgumentException;

class ArrayValidator extends AbstractTranslatableValidator
{
    protected $values = [];

    /**
     * @return array
     */
    public function getValues()
    {
        return $this->values;
    }

    /**
     * @param array $values
     * @return ArrayValidator
     */
    public function setValues($values)
    {
        $this->values = $values;
        return $this;
    }

    public function isValid($mixed, ArrayAccess $error = null)
    {
        if ($mixed !== null && !is_string($mixed)) {
            throw new InvalidArgumentException(t('Invalid type supplied to validator.'));
        }

        if (!in_array($mixed, $this->values)) {
            $error[] = t('The given value is not valid.');
            return false;
        } else {
            return true;
        }
    }
}