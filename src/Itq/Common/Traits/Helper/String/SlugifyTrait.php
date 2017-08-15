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
 * Slugify trait.
 *
 * @author itiQiti Dev Team <opensource@itiqiti.com>
 */
trait SlugifyTrait
{
    /**
     * Removes the stresses from the specified string.
     *
     * @param string $s the string
     *
     * @return string
     */
    abstract protected function removeStringStresses($s);
    /**
     * @param string $string
     *
     * @return string
     */
    protected function slugifyString($string)
    {
        return preg_replace('/[^a-zA-Z0-9]+/', '', $this->removeStringStresses($string));
    }
}
