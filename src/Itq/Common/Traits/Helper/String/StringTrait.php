<?php

/*
 * This file is part of the COMMON package.
 *
 * (c) itiQiti SAS <opensource@itiqiti.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Itq\Common\Traits\Helper\String;

/**
 * String trait.
 *
 * @author itiQiti Dev Team <opensource@itiqiti.com>
 */
trait StringTrait
{
    /**
     * @param string $string
     *
     * @return int
     */
    protected function getStringLength($string)
    {
        return function_exists('mb_strlen') ? mb_strlen($string) : strlen($string);
    }
    /**
     * @param string|mixed $value
     *
     * @return bool
     */
    protected function isNonEmptyString($value)
    {
        return null !== $value && 0 < $this->getStringLength($value);
    }
    /**
     * @param string|mixed $value
     *
     * @return bool
     */
    protected function isEmptyString($value)
    {
        return null === $value || 0 >= $this->getStringLength($value);
    }
}
