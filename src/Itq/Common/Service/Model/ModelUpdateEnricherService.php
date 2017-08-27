<?php

/*
 * This file is part of the COMMON package.
 *
 * (c) itiQiti SAS <opensource@itiqiti.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Itq\Common\Service\Model;

use Itq\Common\Plugin;
use Itq\Common\Traits;
use Itq\Common\Service;

/**
 * @author itiQiti Dev Team <opensource@itiqiti.com>
 */
class ModelUpdateEnricherService extends Base\AbstractModelUpdateEnricherService
{
    use Traits\ServiceAware\MetaDataServiceAwareTrait;
    /**
     * @param Service\MetaDataService $metaDataService
     */
    public function __construct(Service\MetaDataService $metaDataService)
    {
        $this->setMetaDataService($metaDataService);
    }
    /**
     * @param string                              $type
     * @param Plugin\ModelUpdateEnricherInterface $updateEnricher
     *
     * @return $this
     */
    public function addModelUpdateEnricher($type, Plugin\ModelUpdateEnricherInterface $updateEnricher)
    {
        return $this->setArrayParameterKey('updateEnrichers', $type, $updateEnricher);
    }
    /**
     * @return Plugin\ModelUpdateEnricherInterface[]
     */
    public function getModelUpdateEnrichers()
    {
        return $this->getArrayParameter('updateEnrichers');
    }
    /**
     * @param string $type
     *
     * @return Plugin\ModelUpdateEnricherInterface
     */
    public function getModelUpdateEnricher($type)
    {
        return $this->getArrayParameterKey('updateEnrichers', $type);
    }
    /**
     * @param array  $data
     * @param string $class
     * @param array  $options
     *
     * @return array
     */
    public function enrichUpdates($data, $class, array $options = [])
    {
        $enrichments = $this->getMetaDataService()->getModelUpdateEnrichments($class);

        foreach ($data as $k => $v) {
            if (!isset($enrichments[$k])) {
                continue;
            }
            unset($data[$k]);
            foreach ($enrichments[$k] as $enrichment) {
                $this->getModelUpdateEnricher($enrichment['type'])->enrich($data, $k, $v, $options);
            }
        }

        return $data;
    }
}
