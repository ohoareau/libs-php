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
use Exception;

/**
 * Supervision Service.
 *
 * @author itiQiti Dev Team <opensource@itiqiti.com>
 */
class SupervisionService
{
    use Traits\ServiceTrait;
    use Traits\ServiceAware\HttpServiceAwareTrait;
    use Traits\ServiceAware\DateServiceAwareTrait;
    use Traits\ServiceAware\DataProviderServiceAwareTrait;
    use Traits\ParameterAware\ApplicationsParameterAwareTrait;
    /**
     * @param DataProviderService $dataProviderService
     * @param HttpService         $httpService
     * @param DateService         $dateService
     * @param array               $applications
     */
    public function __construct(DataProviderService $dataProviderService, HttpService $httpService, DateService $dateService, array $applications = [])
    {
        $this->setDataProviderService($dataProviderService);
        $this->setHttpService($httpService);
        $this->setDateService($dateService);
        $this->setApplications($applications);
    }
    /**
     * @param string $name
     *
     * @return array
     *
     * @throws Exception
     */
    public function describeApplication($name)
    {
        $a    = $this->getDateService()->getCurrentTime();
        $data = $this->getHttpService()->jsonRequest($this->getApplication($name)['url'])['content'];
        $b    = $this->getDateService()->getCurrentTime();

        $data['supervisionStartTime'] = $a;
        $data['supervisionEndTime']   = $b;
        $data['supervisionDuration']  = round(($b - $a) * 1000);

        return $data;
    }
    /**
     * @param array $options
     *
     * @return array
     */
    public function supervise(array $options = [])
    {
        return $this->provide(null, $options);
    }
    /**
     * @param array $options
     *
     * @return array
     */
    public function identify(array $options = [])
    {
        return $this->provide('identity', $options);
    }
    /**
     * @param string $type
     * @param array  $options
     *
     * @return array
     */
    protected function provide($type, array $options = [])
    {
        return $this->getDataProviderService()->provide(
            sprintf('supervision%s', null === $type ? '' : ('.'.$type)),
            $options
        );
    }
}
