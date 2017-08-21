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

use Itq\Common\Plugin;
use Itq\Common\Traits;
use Itq\Common\Service;

/**
 * @author itiQiti Dev Team <opensource@itiqiti.com>
 */
class ModelService
{
    use Traits\ServiceTrait;
    use Traits\ServiceAware\StorageServiceAwareTrait;
    use Traits\ServiceAware\MetaDataServiceAwareTrait;
    use Traits\ServiceAware\DynamicUrlServiceAwareTrait;
    /**
     * @param Service\MetaDataService   $metaDataService
     * @param Service\StorageService    $storageService
     * @param Service\DynamicUrlService $dynamicUrlService
     */
    public function __construct(
        Service\MetaDataService $metaDataService,
        Service\StorageService $storageService,
        Service\DynamicUrlService $dynamicUrlService
    ) {
        $this->setMetaDataService($metaDataService);
        $this->setStorageService($storageService);
        $this->setDynamicUrlService($dynamicUrlService);
    }
    /**
     * @param Plugin\ModelRefresherInterface $modelRefresher
     *
     * @return $this
     */
    public function addRefresher(Plugin\ModelRefresherInterface $modelRefresher)
    {
        return $this->pushArrayParameterItem('refreshers', $modelRefresher);
    }
    /**
     * @param Plugin\ModelRestricterInterface $modelRestricter
     *
     * @return $this
     */
    public function addRestricter(Plugin\ModelRestricterInterface $modelRestricter)
    {
        return $this->pushArrayParameterItem('restricters', $modelRestricter);
    }
    /**
     * @param Plugin\ModelCleanerInterface $modelCleaner
     *
     * @return $this
     */
    public function addCleaner(Plugin\ModelCleanerInterface $modelCleaner)
    {
        return $this->pushArrayParameterItem('cleaners', $modelCleaner);
    }
    /**
     * @param Plugin\ModelPropertyMutatorInterface $modelPropertyMutator
     *
     * @return $this
     */
    public function addPropertyMutator(Plugin\ModelPropertyMutatorInterface $modelPropertyMutator)
    {
        return $this->pushArrayParameterItem('propertyMutators', $modelPropertyMutator);
    }
    /**
     * @param Plugin\ModelDynamicPropertyBuilderInterface $modelDynamicPropertyBuilder
     *
     * @return $this
     */
    public function addDynamicPropertyBuilder(Plugin\ModelDynamicPropertyBuilderInterface $modelDynamicPropertyBuilder)
    {
        return $this->pushArrayParameterItem('dynamicPropertyBuilders', $modelDynamicPropertyBuilder);
    }
    /**
     * @param string                              $type
     * @param Plugin\ModelUpdateEnricherInterface $modelUpdateEnricher
     *
     * @return $this
     */
    public function addUpdateEnricher($type, Plugin\ModelUpdateEnricherInterface $modelUpdateEnricher)
    {
        return $this->setArrayParameterKey('updateEnrichers', $type, $modelUpdateEnricher);
    }
    /**
     * @param Plugin\ModelFieldListFilterInterface $modelFieldListFilter
     *
     * @return $this
     */
    public function addFieldListFilter(Plugin\ModelFieldListFilterInterface $modelFieldListFilter)
    {
        return $this->pushArrayParameterItem('fieldListFilters', $modelFieldListFilter);
    }
    /**
     * @param Plugin\ModelPropertyLinearizerInterface $modelPropertyLinearizer
     *
     * @return $this
     */
    public function addPropertyLinearizer(Plugin\ModelPropertyLinearizerInterface $modelPropertyLinearizer)
    {
        return $this->pushArrayParameterItem('propertyLinearizers', $modelPropertyLinearizer);
    }
    /**
     * @param Plugin\ModelPropertyAuthorizationCheckerInterface $modelPropertyAuthorizationChecker
     *
     * @return $this
     */
    public function addPropertyAuthorizationChecker(Plugin\ModelPropertyAuthorizationCheckerInterface $modelPropertyAuthorizationChecker)
    {
        return $this->pushArrayParameterItem('propertyAuthorizationCheckers', $modelPropertyAuthorizationChecker);
    }
    /**
     * @return Plugin\ModelRefresherInterface[]
     */
    public function getRefreshers()
    {
        return $this->getArrayParameter('refreshers');
    }
    /**
     * @return Plugin\ModelRestricterInterface[]
     */
    public function getRestricters()
    {
        return $this->getArrayParameter('restricters');
    }
    /**
     * @return Plugin\ModelCleanerInterface[]
     */
    public function getCleaners()
    {
        return $this->getArrayParameter('cleaners');
    }
    /**
     * @return Plugin\ModelPropertyMutatorInterface[]
     */
    public function getPropertyMutators()
    {
        return $this->getArrayParameter('propertyMutators');
    }
    /**
     * @return Plugin\ModelDynamicPropertyBuilderInterface[]
     */
    public function getDynamicPropertyBuilders()
    {
        return $this->getArrayParameter('dynamicPropertyBuilders');
    }
    /**
     * @return Plugin\ModelUpdateEnricherInterface[]
     */
    public function getUpdateEnrichers()
    {
        return $this->getArrayParameter('updateEnrichers');
    }
    /**
     * @return Plugin\ModelFieldListFilterInterface[]
     */
    public function getFieldListFilters()
    {
        return $this->getArrayParameter('fieldListFilters');
    }
    /**
     * @return Plugin\ModelPropertyLinearizerInterface[]
     */
    public function getPropertyLinearizers()
    {
        return $this->getArrayParameter('propertyLinearizers');
    }
    /**
     * @return Plugin\ModelPropertyAuthorizationCheckerInterface[]
     */
    public function getPropertyAuthorizationCheckers()
    {
        return $this->getArrayParameter('propertyAuthorizationCheckers');
    }
    /**
     * @param string $type
     *
     * @return Plugin\ModelUpdateEnricherInterface
     */
    public function getUpdateEnricher($type)
    {
        return $this->getArrayParameterKey('updateEnrichers', $type);
    }
    /**
     * @param mixed $doc
     * @param array $options
     */
    public function restrict($doc, array $options = [])
    {
        foreach ($this->getRestricters() as $restricter) {
            $restricter->restrict($doc, $options);
        }
    }
    /**
     * @param mixed $doc
     * @param array $options
     *
     * @return mixed
     */
    public function refresh($doc, $options = [])
    {
        $this->restrict($doc, $options);

        foreach ($this->getRefreshers() as $refresher) {
            $doc = $refresher->refresh($doc, $options);
        }

        foreach ($doc as $k => $v) {
            if (!is_object($v) || !$this->getMetaDataService()->isModel($v)) {
                continue;
            }
            $doc->$k = $this->refresh($v, $options);
        }

        return $doc;
    }
    /**
     * @param mixed $doc
     * @param array $options
     *
     * @return mixed
     */
    public function clean($doc, $options = [])
    {
        foreach ($this->getCleaners() as $cleaner) {
            $cleaner->clean($doc, $options);
        }

        return $doc;
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
                $this->getUpdateEnricher($enrichment['type'])->enrich($data, $k, $v, $options);
            }
        }

        return $data;
    }
    /**
     * @param string $model
     * @param array  $fields
     * @param array  $options
     *
     * @return array
     */
    public function prepareFields($model, $fields, array $options = [])
    {
        $cleanedFields = [];

        foreach ((is_array($fields) ? $fields : []) as $k => $v) {
            if (is_numeric($k)) {
                if (!is_string($v)) {
                    $v = (string) $v;
                }
                $k = $v;
                $v = true;
            } else {
                if (!is_bool($v)) {
                    $v = (bool) $v;
                }
            }
            $cleanedFields[$k] = $v;
        }

        foreach ($this->getFieldListFilters() as $fieldListFilter) {
            $fieldListFilter->filter($model, $cleanedFields, $options);
        }

        return $cleanedFields;
    }
    /**
     * @param mixed $doc
     * @param array $options
     *
     * @return mixed
     *
     * @throws \Exception
     */
    public function convertObjectToArray($doc, $options = [])
    {
        if (!is_object($doc)) {
            throw $this->createMalformedException('Not a valid object');
        }

        $options         += ['removeNulls' => true];
        $removeNulls      = true === $options['removeNulls'];
        $meta             = $this->getMetaDataService()->getModel($doc);
        $data             = get_object_vars($doc);
        $globalObjectCast = 'stdClass' === get_class($doc);
        $that             = $this;
        $objectLinearizer = function ($doc, $options) use ($that) {
            return $that->convertObjectToArray($doc, $options);
        };

        foreach ($data as $k => $v) {
            if ($removeNulls && null === $v) {
                unset($data[$k]);
                continue;
            }
            if (is_string($v) && false !== strpos($v, '*cleared*')) {
                $v = null;
                $doc->$k = $v;
            }
            foreach ($this->getPropertyLinearizers() as $propertyLinearizer) {
                if (!$propertyLinearizer->supports($data, $k, $v, $meta, $options)) {
                    continue;
                }
                $propertyLinearizer->linearize($data, $k, $v, $meta, $objectLinearizer, $options);
            }
        }

        return (true === $globalObjectCast) ? ((object) $data) : $data;
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
    /**
     * @param mixed $doc
     * @param array $data
     * @param array $options
     *
     * @return mixed
     */
    public function populateObject($doc, $data = [], $options = [])
    {
        if (isset($data['_id']) && !isset($data['id'])) {
            $data['id'] = (string) $data['_id'];
            unset($data['_id']);
        } elseif (isset($data['id'])) {
            $data['id'] = (string) $data['id'];
        }

        $ctx     = (object) ['models' => []];
        $modelId = $this->getMetaDataService()->getModelIdForClass($doc);

        foreach ($data as $k => $v) {
            if (!isset($ctx->models[$modelId])) {
                $ctx->models[$modelId] = $this->getMetaDataService()->fetchModelDefinition($doc);
            }

            $m = &$ctx->models[$modelId];

            if (!property_exists($doc, $k)) {
                continue;
            }

            foreach ($this->getPropertyAuthorizationCheckers() as $propertyAuthorizatonChecker) {
                if (!$propertyAuthorizatonChecker->isAllowed($doc, $k, isset($options['operation']) ? $options['operation'] : null, $options)) {
                    $doc->$k = null;
                }
            }

            $that          = $this;
            $objectMutator = function (array $data, $doc, array $options = []) use ($that) {
                return $that->populateObject($doc, $data, $options);
            };

            foreach ($this->getPropertyMutators() as $propertyMutator) {
                if (!$propertyMutator->supports($doc, $k, $m)) {
                    continue;
                }
                $v = $propertyMutator->mutate($doc, $k, $v, $m, $data, $objectMutator, $options);
            }

            $doc->$k = $v;
        }

        if (isset($options['requestedFields']) && is_array($options['requestedFields'])) {
            foreach (array_keys($options['requestedFields']) as $requestedField) {
                $this->mutateRequestedFieldIfNecessary(
                    $modelId,
                    $doc,
                    is_int($requestedField) ? $options['requestedFields'][$requestedField] : $requestedField,
                    $ctx,
                    $options
                );
            }
        }

        $this->getStorageService()->populate($doc, $this->getMetaDataService()->getModelStorages($doc), $options);

        return $doc;
    }
    /**
     * @param mixed  $doc
     * @param mixed  $data
     * @param string $propertyName
     * @param array  $options
     *
     * @return mixed
     *
     * @throws \Exception
     */
    public function populateObjectProperty($doc, $data, $propertyName, $options = [])
    {
        if (!property_exists($doc, $propertyName)) {
            throw $this->createRequiredException("Property '%s' does not exist on %s", $propertyName, get_class($doc));
        }

        $doc->$propertyName = $data;

        $this->getStorageService()->populate($doc, $this->getMetaDataService()->getModelStorages($doc), $options);

        return $doc->$propertyName;
    }
    /**
     * @param string $modelId
     * @param mixed  $doc
     * @param string $requestedField
     * @param object $ctx
     * @param array  $options
     *
     * @return void
     */
    protected function mutateRequestedFieldIfNecessary($modelId, $doc, $requestedField, $ctx, &$options)
    {
        if (false !== ($pos = strpos($requestedField, '.'))) {
            $property = substr($requestedField, 0, $pos);
            if (property_exists($doc, $property)) {
                $subDoc = $doc->$property;
                if (is_object($subDoc)) {
                    if ($this->getMetaDataService()->isModel($subDoc)) {
                        $this->mutateRequestedFieldIfNecessary(
                            $this->getMetaDataService()->getModelIdForClass($subDoc),
                            $subDoc,
                            substr($requestedField, $pos + 1),
                            $ctx,
                            $options
                        );

                        return;
                    }
                }
            }

            return;
        }

        if (!isset($ctx->models[$modelId])) {
            $ctx->models[$modelId] = $this->getMetaDataService()->fetchModelDefinition($doc);
        }

        $m = &$ctx->models[$modelId];

        if (!property_exists($doc, $requestedField) || isset($doc->$requestedField)) {
            return;
        }

        foreach ($this->getDynamicPropertyBuilders() as $dynamicPropertyBuilder) {
            if (!$dynamicPropertyBuilder->supports($doc, $requestedField, $m)) {
                continue;
            }
            $doc->$requestedField = $dynamicPropertyBuilder->build($doc, $requestedField, $m);
        }
    }
}
