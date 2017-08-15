<?php

/*
 * This file is part of the COMMON package.
 *
 * (c) itiQiti SAS <opensource@itiqiti.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Itq\Common\Plugin\UniqueCodeGeneratorAlgorithm\Base;

use Itq\Common\Plugin\Base\AbstractPlugin;

/**
 * @author itiQiti Dev Team <opensource@itiqiti.com>
 */
abstract class AbstractUniqueCodeGeneratorAlgorithm extends AbstractPlugin
{
    /**
     * @param int    $n
     * @param string $mode
     *
     * @return null|string
     */
    protected function char($n = 1, $mode = null)
    {
        $s = null;

        for ($i = 0; $i < $n; $i++) {
            switch ($mode) {
                default:
                case 'lower':
                    $offset = 97;
                    break;
                case 'upper':
                    $offset = 65;
                    break;
                case 'both':
                    $offset = $this->randomAlternative(97, 65);
                    break;
            }
            $s .= chr($offset + rand(0, 25));
        }

        return $s;
    }
    /**
     * @param int $n
     *
     * @return null|string
     */
    protected function digit($n = 1)
    {
        $s = null;

        for ($i = 0; $i < $n; $i++) {
            $s .= (string) rand(0, 9);
        }

        return $s;
    }
    /**
     * @param int    $n
     * @param string $mode
     *
     * @return null|string
     */
    protected function charOrDigit($n = 1, $mode = null)
    {
        $chars = [];

        for ($i = 0; $i < $n; $i++) {
            $chars[] = $this->randomBool() ? $this->char(1, $mode) : $this->digit(1);
        }

        return join('', $chars);
    }
    /**
     * @param int    $n
     * @param string $mode
     *
     * @return string
     */
    protected function shuffledCharsOrDigits($n = 2, $mode = null)
    {
        return str_shuffle($this->charOrDigit($n, $mode));
    }
    /**
     * @param string $a
     * @param string $b
     *
     * @return mixed
     */
    protected function randomAlternative($a, $b)
    {
        return $this->randomBool() ? $a : $b;
    }
    /**
     * @return bool
     */
    protected function randomBool()
    {
        return 1 === rand(0, 1);
    }
}
