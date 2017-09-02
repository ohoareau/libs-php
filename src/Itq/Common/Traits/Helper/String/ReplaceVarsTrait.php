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

use Closure;

/**
 * Replace Vars trait.
 *
 * @author itiQiti Dev Team <opensource@itiqiti.com>
 */
trait ReplaceVarsTrait
{
    /**
     * @param mixed $data
     * @param array $params
     * @param array $options
     *
     * @return array|string|object
     */
    protected function replaceVars($data, $params, array $options = [])
    {
        $options += ['pattern' => '/\{([^\}]+)\}/'];

        return $this->replaceVarsCallback(
            $data,
            $params,
            function (&$data, &$params, array &$options) {
                $matches = null;
                if (0 < preg_match_all($options['pattern'], $data, $matches)) {
                    foreach ($matches[1] as $i => $match) {
                        $data = str_replace(
                            $matches[0][$i],
                            isset($params[$match]) ? $params[$match] : null,
                            $data
                        );
                    }
                }
            },
            $options
        );
    }
    /**
     * @param mixed   $data
     * @param array   $params
     * @param Closure $callback
     * @param array   $options
     *
     * @return array|string|object
     */
    protected function replaceVarsCallback($data, $params, Closure $callback, array $options = [])
    {
        if (is_array($data)) {
            foreach ($data as $k => $v) {
                unset($data[$k]);
                $data[$this->replaceVarsCallback($k, $params, $callback, $options)] = $this->replaceVarsCallback($v, $params, $callback, $options);
            }

            return $data;
        }
        if (is_object($data) || is_numeric($data)) {
            return $data;
        }
        if (is_string($data)) {
            $callback($data, $params, $options);
        }

        return $data;
    }
}
