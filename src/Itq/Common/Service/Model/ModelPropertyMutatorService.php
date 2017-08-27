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
use Itq\Common\ModelInterface;
use Itq\Common\ObjectPopulatorInterface;

/**
 * @author itiQiti Dev Team <opensource@itiqiti.com>
 */
class ModelPropertyMutatorService extends Base\AbstractModelPropertyMutatorService
{
    use Traits\ServiceAware\MetaDataServiceAwareTrait;
    use Traits\ServiceAware\Model\ModelPropertyAuthorizationCheckerServiceAwareTrait;
    /**
     * @param Service\MetaDataService                           $metaDataService
     * @param ModelPropertyAuthorizationCheckerServiceInterface $modelPropertyAuthorizationCheckerService
     */
    public function __construct(
        Service\MetaDataService $metaDataService,
        ModelPropertyAuthorizationCheckerServiceInterface $modelPropertyAuthorizationCheckerService
    ) {
        $this->setMetaDataService($metaDataService);
        $this->setModelPropertyAuthorizationCheckerService($modelPropertyAuthorizationCheckerService);
    }
    /**
     * @param ModelInterface           $doc
     * @param array                    $data
     * @param object                   $ctx
     * @param ObjectPopulatorInterface $objectPopulator
     * @param array                    $options
     *
     * @return void
     */
    public function mutate($doc, $data, $ctx, ObjectPopulatorInterface $objectPopulator, array $options = [])
    {
        $modelId = $this->getMetaDataService()->getModelIdForClass($doc);

        foreach ($data as $k => $v) {
            if (!isset($ctx->models[$modelId])) {
                $ctx->models[$modelId] = $this->getMetaDataService()->fetchModelDefinition($doc);
            }

            $m = &$ctx->models[$modelId];

            if (!property_exists($doc, $k)) {
                continue;
            }

            if (!$this->getModelPropertyAuthorizationCheckerService()->isPropertyOperationAllowed(
                $doc,
                $k,
                isset($options['operation']) ? $options['operation'] : null,
                $options
            )) {
                $doc->$k = null;
            }

            $doc->$k = $this->mutateProperty($doc, $k, $v, $m, $data, $objectPopulator, $options);
        }
    }
    /**
     * @param ModelInterface           $doc
     * @param string                   $k
     * @param mixed                    $v
     * @param array                    $m
     * @param array                    $data
     * @param ObjectPopulatorInterface $objectPopulator
     * @param array                    $options
     *
     * @return mixed
     */
    public function mutateProperty($doc, $k, $v, $m, $data, ObjectPopulatorInterface $objectPopulator, $options)
    {
        foreach ($this->getModelPropertyMutators() as $propertyMutator) {
            if (!$propertyMutator->supports($doc, $k, $m)) {
                continue;
            }
            $v = $propertyMutator->mutate($doc, $k, $v, $m, $data, $objectPopulator, $options);
        }

        return $v;
    }
}
