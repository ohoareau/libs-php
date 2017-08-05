<?php

/*
 * This file is part of the COMMON package.
 *
 * (c) itiQiti SAS <cto@itiqiti.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Itq\Common;

/**
 * @author Olivier Hoareau <olivier@itiqiti.com>
 */
interface PeriodStatsInterface
{
    /**
     * @var string
     */
    public function setId($id);
    /**
     * @var string
     */
    public function setPeriodUnit($periodUnit);
    /**
     * @var string
     */
    public function setModelType($modelType);
    /**
     * @var string
     */
    public function setModelId($modelId);
    /**
     * @var int
     */
    public function setBuildStartTime($buildStartTime);
    /**
     * @var int
     */
    public function setBuildEndTime($buildEndTime);
    /**
     * @var int
     */
    public function setBuildDuration($buildDuration);
    /**
     * @var \DateTime
     */
    public function setStartDate(\DateTime $startDate);
    /**
     * @var \DateTime
     */
    public function setEndDate(\DateTime $endDate);
    /**
     * @var \DateTime
     */
    public function setGmtStartDate(\DateTime $gmtStartDate);
    /**
     * @var \DateTime
     */
    public function setGmtEndDate(\DateTime $gmtEndDate);
    /**
     * @var \DateTime
     */
    public function setBuildDate(\DateTime $buildDate);
    /**
     * @var array
     */
    public function setSeries(array $series);
}
