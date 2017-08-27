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

use Itq\Common\Traits;
use Itq\Common\Service;

/**
 * @author itiQiti Dev Team <opensource@itiqiti.com>
 */
class ModelDynamicUrlBuilderService extends Base\AbstractModelDynamicUrlBuilderService
{
    use Traits\ServiceAware\MetaDataServiceAwareTrait;
    use Traits\ServiceAware\DynamicUrlServiceAwareTrait;
    /**
     * @param Service\MetaDataService   $metaDataService
     * @param Service\DynamicUrlService $dynamicUrlService
     */
    public function __construct(
        Service\MetaDataService $metaDataService,
        Service\DynamicUrlService $dynamicUrlService
    ) {
        $this->setMetaDataService($metaDataService);
        $this->setDynamicUrlService($dynamicUrlService);
    }
    /**
     * @param mixed  $doc
     * @param string $property
     * @param array  $options
     *
     * @return mixed
     */
    public function buildDynamicUrl($doc, $property, array $options = [])
    {
        return $this->getDynamicUrlService()->compute(
            $doc,
            $this->getMetaDataService()->getModelPropertyDynamicUrl($doc, $property),
            $options
        );
    }
}
