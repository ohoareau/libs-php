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
use Itq\Common\Service;
use Itq\Common\PeriodStatsInterface;

/**
 * Period Stats Builder Service.
 *
 * @author itiQiti Dev Team <opensource@itiqiti.com>
 */
class PeriodStatsBuilderService
{
    use Traits\ServiceTrait;
    use Traits\ServiceAware\CallableServiceAwareTrait;
    /**
     * @param Service\CallableService $callableService
     */
    public function __construct(Service\CallableService $callableService)
    {
        $this->setCallableService($callableService);
    }
    /**
     * Register a period stats builder for the specified model (replace if exist).
     *
     * @param string   $type
     * @param callable $callable
     * @param array    $options
     *
     * @return $this
     *
     * @throws \Exception
     */
    public function register($type, $callable, array $options = [])
    {
        $this->getCallableService()->registerByType('periodStatsBuilder', $type, $callable, $options);

        return $this;
    }
    /**
     * Return the period stats builder registered for the specified model.
     *
     * @param string $type
     *
     * @return callable
     *
     * @throws \Exception if no period stats builder registered for this name
     */
    public function get($type)
    {
        return $this->getCallableService()->getByType('periodStatsBuilder', $type);
    }
    /**
     * Tests if a period stats builder has been registered for the specified model.
     *
     * @param string $type
     *
     * @return bool
     */
    public function has($type)
    {
        return $this->getCallableService()->hasByType('periodStatsBuilder', $type);
    }
    /**
     * @param PeriodStatsInterface $periodStats
     * @param string               $type
     * @param string               $id
     * @param string               $periodUnit
     * @param \DateTime            $startDate
     * @param \DateTime            $endDate
     * @param array                $options
     *
     * @return PeriodStatsInterface
     *
     * @throws \Exception
     */
    public function build(PeriodStatsInterface $periodStats, $type, $id, $periodUnit, \DateTime $startDate, \DateTime $endDate, array $options = [])
    {
        return $this->buildPeriodStats(
            $periodStats,
            $type,
            $id,
            preg_replace('/[^a-z0-9]+/', '', strtolower($periodUnit)),
            $startDate,
            $endDate,
            $options
        );
    }
    /**
     * @param PeriodStatsInterface $periodStats
     * @param string               $type
     * @param string               $id
     * @param string               $periodUnit
     * @param \DateTime            $startDate
     * @param \DateTime            $endDate
     * @param array                $options
     *
     * @return $this
     *
     * @throws \Exception
     */
    protected function buildPeriodStats(PeriodStatsInterface $periodStats, $type, $id, $periodUnit, \DateTime $startDate, \DateTime $endDate, array $options = [])
    {
        $startTime = microtime(true);

        $periodStats->setId(md5(sprintf('%s-%s-%s-%s-%s', $type, $id, $periodUnit, $startDate->format('c'), $endDate->format('c'))));
        $periodStats->setModelType($type);
        $periodStats->setModelId($id);
        $periodStats->setPeriodUnit($periodUnit);
        $periodStats->setStartDate($startDate);
        $periodStats->setEndDate($endDate);
        $periodStats->setBuildStartTime($startTime);
        $periodStats->setGmtStartDate($startDate->setTimezone(new \DateTimeZone('UTC')));
        $periodStats->setGmtEndDate($endDate->setTimezone(new \DateTimeZone('UTC')));

        if ($this->has($type)) {
            $this->getCallableService()->executeByType('periodStatsBuilder', $type, [$periodStats, $options]);
        }

        $endTime = microtime(true);

        $periodStats->setBuildEndTime($endTime);
        $periodStats->setBuildDuration($endTime - $startTime);
        $periodStats->setBuildDate(new \DateTime());

        return $this;
    }
}
