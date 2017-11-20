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
class ModelDynamicPropertyBuilderService extends Base\AbstractModelDynamicPropertyBuilderService
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
     * @param object $doc
     * @param array  $requestedFields
     * @param object $ctx
     * @param array  $options
     *
     * @return void
     */
    public function build($doc, $requestedFields, $ctx, array $options = [])
    {
        if (!is_array($requestedFields)) {
            return;
        }
        $modelId = $this->getMetaDataService()->getModelIdForClass(isset($options['originalModel']) ? $options['originalModel'] : $doc);

        foreach (array_keys($requestedFields) as $requestedField) {
            $this->buildProperty(
                $modelId,
                $doc,
                is_int($requestedField) ? $requestedFields[$requestedField] : $requestedField,
                $ctx,
                $options
            );
        }
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
    public function buildProperty($modelId, $doc, $requestedField, $ctx, array $options = [])
    {
        if (false !== ($pos = strpos($requestedField, '.'))) {
            $property = substr($requestedField, 0, $pos);
            if (property_exists($doc, $property)) {
                $subDoc = $doc->$property;
                if (is_object($subDoc)) {
                    if ($this->getMetaDataService()->isModel($subDoc)) {
                        $this->buildProperty(
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
            $ctx->models[$modelId] = $this->getMetaDataService()->fetchModelDefinition(isset($options['originalModel']) ? $options['originalModel'] : $doc);
        }

        $m = &$ctx->models[$modelId];

        if (!property_exists($doc, $requestedField) || isset($doc->$requestedField)) {
            return;
        }

        foreach ($this->getModelDynamicPropertyBuilders() as $dynamicPropertyBuilder) {
            if (!$dynamicPropertyBuilder->supports($doc, $requestedField, $m)) {
                continue;
            }
            $doc->$requestedField = $dynamicPropertyBuilder->build($doc, $requestedField, $m);
        }
    }
}
