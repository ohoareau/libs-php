<?php

/*
 * This file is part of the COMMON package.
 *
 * (c) itiQiti SAS <opensource@itiqiti.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Itq\Common;

/**
 * @author itiQiti Dev Team <opensource@itiqiti.com>
 */
interface PeriodStatsInterface
{
    /**
     * @param string $id
     */
    public function setId($id);
    /**
     * @param string $periodUnit
     */
    public function setPeriodUnit($periodUnit);
    /**
     * @param string $modelType
     */
    public function setModelType($modelType);
    /**
     * @param string $modelId
     */
    public function setModelId($modelId);
    /**
     * @param int $buildStartTime
     */
    public function setBuildStartTime($buildStartTime);
    /**
     * @param int $buildEndTime
     */
    public function setBuildEndTime($buildEndTime);
    /**
     * @param int $buildDuration
     */
    public function setBuildDuration($buildDuration);
    /**
     * @param \DateTime $startDate
     */
    public function setStartDate(\DateTime $startDate);
    /**
     * @param \DateTime $endDate
     */
    public function setEndDate(\DateTime $endDate);
    /**
     * @param \DateTime $gmtStartDate
     */
    public function setGmtStartDate(\DateTime $gmtStartDate);
    /**
     * @param \DateTime $gmtEndDate
     */
    public function setGmtEndDate(\DateTime $gmtEndDate);
    /**
     * @param \DateTime $buildDate
     */
    public function setBuildDate(\DateTime $buildDate);
    /**
     * @param array $series
     */
    public function setSeries(array $series);
}
