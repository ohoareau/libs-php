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
 * Supervision Service.
 *
 * @author itiQiti Dev Team <opensource@itiqiti.com>
 */
class SupervisionService
{
    use Traits\ServiceTrait;
    use Traits\ServiceAware\DataProviderServiceAwareTrait;
    /**
     * @param DataProviderService $dataProviderService
     */
    public function __construct(DataProviderService $dataProviderService)
    {
        $this->setDataProviderService($dataProviderService);
    }
    /**
     * @param array $options
     *
     * @return array
     */
    public function supervise(array $options = [])
    {
        return $this->provide('supervision', $options);
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
        return $this->getDataProviderService()->provide(sprintf('supervision.%s', $type), $options);
    }
}
