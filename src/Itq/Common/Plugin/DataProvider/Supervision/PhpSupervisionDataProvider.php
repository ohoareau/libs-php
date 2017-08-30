<?php

/*
 * This file is part of the COMMON package.
 *
 * (c) itiQiti SAS <opensource@itiqiti.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Itq\Common\Plugin\DataProvider\Supervision;

use Itq\Common\Traits;
use Itq\Common\Service;

/**
 * @author itiQiti Dev Team <opensource@itiqiti.com>
 */
class PhpSupervisionDataProvider extends Base\AbstractSupervisionDataProvider
{
    use Traits\ServiceAware\PhpServiceAwareTrait;
    use Traits\ServiceAware\DateServiceAwareTrait;
    use Traits\ServiceAware\SystemServiceAwareTrait;
    /**
     * @param Service\PhpService    $phpService
     * @param Service\SystemService $systemService
     * @param Service\DateService   $dateService
     */
    public function __construct(
        Service\PhpService $phpService,
        Service\SystemService $systemService,
        Service\DateService $dateService
    ) {
        $this->setPhpService($phpService);
        $this->setSystemService($systemService);
        $this->setDateService($dateService);
    }
    /**
     * @param array $options
     *
     * @return array
     */
    public function provide(array $options = [])
    {
        $startTime   = $this->getPhpService()->getConstant('APP_TIME_START');
        $currentTime = $this->getSystemService()->getCurrentTime();

        return [
            'currentTime'   => $currentTime,
            'hostName'      => $this->getSystemService()->getHostName(),
            'date'          => $this->getDateService()->getCurrentDate(),
            'php'           => $this->getPhpService()->describe(),
            'startDuration' => (null !== $startTime) ? ($currentTime - $startTime) : null,
            'startTime'     => $startTime,
        ];
    }
}
