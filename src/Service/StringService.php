<?php

/*
 * This file is part of the COMMON package.
 *
 * (c) itiQiti SAS <opensource@itiqiti.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Itq\Common\Service;

use Itq\Common\Traits;

/**
 * @author itiQiti Dev Team <opensource@itiqiti.com>
 */
class StringService
{
    use Traits\ServiceTrait;
    /**
     * Removes the stresses from the specified string.
     *
     * @param string $s the string
     *
     * @return string
     */
    public function removeStresses($s)
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
    /**
     * @param string $message
     *
     * @return string
     */
    public function normalizeKeyword($message)
    {
        return strtoupper(preg_replace('/[^a-zA-Z0-9]+/', '', $this->removeStresses($message)));
    }
    /**
     * @param \Closure $tester
     * @param string   $algo
     * @param string   $prefix
     *
     * @return string
     *
     * @throws \Exception
     */
    public function generateUniqueCode(\Closure $tester, $algo, $prefix = null)
    {
        $i = 0;

        do {
            if (10 < $i) {
                throw $this->createDuplicatedException('Too much iteration (%d) for generating unique code', $i);
            }
            $value = $this->generateCode($algo, $prefix);
            $i++;
        } while ($tester($value));

        return $value;
    }
    /**
     * @param string $algo
     * @param string $prefix
     *
     * @return string
     */
    public function generateCode($algo, $prefix = null)
    {
        switch ($algo) {
            case '5LD':
                $chars = [];
                for ($k = 0; $k < 5; $k++) {
                    $chars[] = rand(0, 1) === 1 ? chr(65 + rand(0, 25)) : ((string) rand(0, 9));
                }
                shuffle($chars);
                $suffix = join('', $chars);
                break;
            case '4ld':
                $chars = [];
                for ($k = 0; $k < 4; $k++) {
                    $chars[] = rand(0, 1) === 1 ? chr(65 + rand(0, 25)) : ((string) rand(0, 9));
                }
                shuffle($chars);
                $suffix = strtolower(join('', $chars));
                break;
            case '3l3d':
                $suffix = strtolower(chr(65 + rand(0, 25)).chr(65 + rand(0, 25)).chr(65 + rand(0, 25)).(string) rand(0, 9).(string) rand(0, 9).(string) rand(0, 9));
                break;
            case '2L1D':
                $suffix = chr(65 + rand(0, 25)).chr(65 + rand(0, 25)).(string) rand(0, 9);
                break;
            default:
            case '3L3D':
                $suffix = chr(65 + rand(0, 25)).chr(65 + rand(0, 25)).chr(65 + rand(0, 25)).(string) rand(0, 9).(string) rand(0, 9).(string) rand(0, 9);
                break;
        }

        return $prefix.$suffix;
    }
}
