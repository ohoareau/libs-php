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
use Itq\Common\Plugin\TrackerInterface;

/**
 * Tracker Service.
 *
 * @author itiQiti Dev Team <opensource@itiqiti.com>
 */
class TrackerService
{
    use Traits\ServiceTrait;
    /**
     * @param string           $type
     * @param TrackerInterface $tracker
     *
     * @return $this
     */
    public function addTracker($type, TrackerInterface $tracker)
    {
        return $this->setArrayParameterKey('trackers', $type, $tracker);
    }
    /**
     * @return array
     */
    public function getTrackers()
    {
        return $this->getArrayParameter('trackers');
    }
    /**
     * @param string $type
     *
     * @return TrackerInterface
     */
    public function getTracker($type)
    {
        return $this->getArrayParameterKey('trackers', $type);
    }
    /**
     * @param string $type
     * @param array  $definition
     * @param mixed  $data
     * @param array  $options
     *
     * @return $this
     */
    public function track($type, array $definition, $data, array $options = [])
    {
        $this->getTracker($type)->track($definition, $data, $options);

        return $this;
    }
}
