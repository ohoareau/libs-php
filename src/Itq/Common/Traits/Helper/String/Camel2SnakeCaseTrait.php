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
 * Camel2SnakeCase trait.
 *
 * @author itiQiti Dev Team <opensource@itiqiti.com>
 */
trait Camel2SnakeCaseTrait
{
    /**
     * @param string $string
     *
     * @return int
     */
    abstract protected function getStringLength($string);
    /**
     * @param string $string
     *
     * @return string
     */
    protected function convertCamelCaseStringToSnakeCaseString($string)
    {
        $n      = $this->getStringLength($string);
        $first  = true;
        $result = null;

        for ($i = 0; $i < $n; $i++) {
            if (ord($string{$i}) < 97) {
                if (!$first) {
                    $result .= '_';
                }
            }
            $result .= strtolower($string{$i});
            $first   = false;
        }

        return $result;
    }
}
