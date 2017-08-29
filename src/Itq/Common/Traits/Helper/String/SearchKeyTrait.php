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
 * SearchKey trait.
 *
 * @author itiQiti Dev Team <opensource@itiqiti.com>
 */
trait SearchKeyTrait
{
    /**
     * @param string $string
     *
     * @return string
     */
    abstract public function removeStresses($string);
    /**
     * @param string $value
     *
     * @return string
     */
    protected function searchKeyize($value)
    {
        return str_replace(
            ' ',
            '-',
            trim(
                preg_replace(
                    '/[^a-z0-9]+/',
                    ' ',
                    trim(strtolower($this->removeStresses($value)))
                )
            )
        );
    }
}
