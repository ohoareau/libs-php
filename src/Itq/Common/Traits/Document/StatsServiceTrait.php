<?php

/*
 * This file is part of the COMMON package.
 *
 * (c) itiQiti SAS <opensource@itiqiti.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Itq\Common\Traits\Document;

/**
 * @author itiQiti Dev Team <opensource@itiqiti.com>
 */
trait StatsServiceTrait
{
    /**
     * @param string $id
     * @param string $key
     * @param int    $value
     * @param array  $options
     *
     * @return $this
     */
    public function incrementStat($id, $key, $value = 1, array $options = [])
    {
        return $this->incrementStats($id, [$key => $value], $options);
    }
    /**
     * @param string $id
     * @param array  $stats
     * @param array  $options
     *
     * @return $this
     */
    public function incrementStats($id, array $stats, array $options = [])
    {
        if (!count($stats)) {
            return $this;
        }

        $options += ['suffix' => null, 'prefix' => null];

        $data = [];

        foreach ($stats as $k => $v) {
            $data['stats.'.$options['prefix'].preg_replace('/[^a-z0-9_\:\-]+/i', '_', $k).$options['suffix']] = $v;
            unset($stats[$k]);
        }

        unset($stats);

        $this->getRepository()->alter($id, ['$inc' => $data]);

        unset($data);

        return $this;
    }
    /**
     * @param string $id
     * @param array  $stats
     * @param array  $options
     *
     * @return $this
     */
    public function setStats($id, array $stats, array $options = [])
    {
        if (!count($stats)) {
            return $this;
        }

        $options += ['suffix' => null, 'prefix' => null];

        $data = [];

        foreach ($stats as $k => $v) {
            $data['stats.'.$options['prefix'].preg_replace('/[^a-z0-9_\:\-]+/i', '_', $k).$options['suffix']] = $v;
            unset($stats[$k]);
        }

        unset($stats);

        $this->getRepository()->alter($id, ['$set' => $data]);

        unset($data);

        return $this;
    }
}
