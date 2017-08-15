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
 * RemoveStresses trait.
 *
 * @author itiQiti Dev Team <opensource@itiqiti.com>
 */
trait RemoveStressesTrait
{
    /**
     * Removes the stresses from the specified string.
     *
     * @param string $s the string
     *
     * @return string
     */
    protected function removeStringStresses($s)
    {
        $s = str_replace(['é', 'è', 'ê', 'ë', 'ę', 'ė', 'ē'], 'e', $s);
        $s = str_replace(['É', 'È', 'Ê', 'Ë', 'Ę', 'Ė', 'Ē'], 'E', $s);
        $s = str_replace(['à', 'â', 'ä', 'ã', 'ª', 'á', 'å', 'ā'], 'a', $s);
        $s = str_replace(['À', 'Â', 'Ä', 'Ã', 'Á', 'Å', 'Ā'], 'A', $s);
        $s = str_replace(['ç', 'ć', 'č'], 'c', $s);
        $s = str_replace(['Ç', 'Ć', 'Č'], 'C', $s);
        $s = str_replace(['ÿ', 'ñ', 'ń'], ['y', 'n', 'n'], $s);
        $s = str_replace(['Ÿ', 'Ñ', 'Ń'], ['Y', 'N', 'N'], $s);
        $s = str_replace(['ù', 'ú', 'ū', 'û', 'ü'], 'u', $s);
        $s = str_replace(['Û', 'Ù', 'Ü', 'Ú', 'Ū'], 'U', $s);
        $s = str_replace(['ì', 'í', 'į', 'ī', 'î', 'ï'], 'i', $s);
        $s = str_replace(['Î', 'Ï', 'Ì', 'Í', 'Į', 'Ī'], 'I', $s);
        $s = str_replace(['ò', 'ô', 'ö', 'õ', 'º', 'ó', 'ø', 'ō'], 'o', $s);
        $s = str_replace(['Ô', 'Ö', 'Ò', 'Ó', 'Õ', 'Ø', 'Ō'], 'O', $s);
        $s = str_replace(['œ', 'Œ', 'æ', 'Æ'], ['oe', 'OE', 'ae', 'AE'], $s);

        return $s;
    }
}
