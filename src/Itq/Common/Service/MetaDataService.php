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

use Exception;
use Itq\Common\Traits;
use Itq\Common\PreprocessorContext;

/**
 * @author itiQiti Dev Team <opensource@itiqiti.com>
 */
class MetaDataService
{
    use Traits\ServiceTrait;
    use Traits\PreprocessorContextAwareTrait;
    use Traits\PluginAware\ModelDescriptorPluginAwareTrait;
    /**
     * @param string|PreprocessorContext $preprocessorContext
     */
    public function __construct($preprocessorContext)
    {
        if (is_string($preprocessorContext)) {
            /** @noinspection PhpIncludeInspection */

            $preprocessorContext = require $preprocessorContext;
        }

        $this->setPreprocessorContext($preprocessorContext);
    }
    /**
     * @param string|object $class
     *
     * @return array
     */
    public function getModel($class)
    {
        return $this->getPreprocessorContext()->getModel($class);
    }
    /**
     * @return string[]
     */
    public function getModelIds()
    {
        return $this->getPreprocessorContext()->getModelIds();
    }
    /**
     * @param string|object $class
     *
     * @return array
     */
    public function getModelDescription($class)
    {
        $id          = $this->getModelIdForClass($class);
        $description = ['id' => $id, 'class' => is_object($class) ? get_class($class) : (string) $class];

        foreach ($this->getModelDescriptors() as $type => $modelDescriptor) {
            $description += $modelDescriptor->describe($id, ['type' => $type, 'class' => $class]);
        }

        return $this->fetchModelDefinition($class) + $description;
    }
    /**
     * @param string $prefix
     *
     * @return array
     *
     * @throws Exception
     */
    public function getRetrievableStorageByPrefix($prefix)
    {
        return $this->getPreprocessorContext()->getRetrievableStorageByPrefix($prefix);
    }
    /**
     * @param string $target
     *
     * @return array
     *
     * @throws Exception
     */
    public function getSdkServices($target = 'php')
    {
        return $this->getPreprocessorContext()->getSdkServices($target);
    }
    /**
     * @param string|object $class
     *
     * @return array
     */
    public function getModelRestricts($class)
    {
        return $this->getPreprocessorContext()->getModelRestricts($class);
    }
    /**
     * @param string|object $class
     *
     * @return array
     */
    public function getModelUpdateEnrichments($class)
    {
        return $this->getPreprocessorContext()->getModelUpdateEnrichments($class);
    }
    /**
     * @param string|object $class
     * @param string        $property
     *
     * @return array
     */
    public function getModelPropertyDynamicUrl($class, $property)
    {
        return $this->getPreprocessorContext()->getModelPropertyDynamicUrl($class, $property);
    }
    /**
     * @param string|object $class
     * @param string        $property
     *
     * @return array
     */
    public function getModelPropertyRequirements($class, $property)
    {
        return $this->getPreprocessorContext()->getModelPropertyRequirements($class, $property);
    }
    /**
     * @param string|object $class
     * @param array         $options
     *
     * @return array
     */
    public function getModelEmbeddedReferenceLinks($class, array $options = [])
    {
        return $this->getPreprocessorContext()->getModelEmbeddedReferenceLinks($class, $options);
    }
    /**
     * @param string $operation
     *
     * @return array
     */
    public function getOperationTrackers($operation)
    {
        return $this->getPreprocessorContext()->getOperationTrackers($operation);
    }
    /**
     * @param string|object $class
     *
     * @return array
     */
    public function getModelEmbeddedReferences($class)
    {
        return $this->getPreprocessorContext()->getModelEmbeddedReferences($class);
    }
    /**
     * @param string|object $class
     *
     * @return array
     */
    public function getModelReferences($class)
    {
        return $this->getPreprocessorContext()->getModelReferences($class);
    }
    /**
     * @param string $id
     *
     * @return string
     *
     * @throws Exception
     */
    public function getModelClassForId($id)
    {
        return $this->getPreprocessorContext()->getModelClassForId($id);
    }
    /**
     * @param string|object $class
     *
     * @return array
     */
    public function getModelHashLists($class)
    {
        return $this->getPreprocessorContext()->getModelHashLists($class);
    }
    /**
     * @param string|object $class
     *
     * @return array
     */
    public function getModelTagLists($class)
    {
        return $this->getPreprocessorContext()->getModelTagLists($class);
    }
    /**
     * @param string|object $class
     *
     * @return array
     */
    public function getModelBasicLists($class)
    {
        return $this->getPreprocessorContext()->getModelBasicLists($class);
    }
    /**
     * @param string|object $class
     *
     * @return array
     */
    public function getModelEmbeddeds($class)
    {
        return $this->getPreprocessorContext()->getModelEmbeddeds($class);
    }
    /**
     * @param string|object $class
     *
     * @return string
     *
     * @throws Exception
     */
    public function getModelIdForClass($class)
    {
        return $this->getPreprocessorContext()->getModelIdForClass($class);
    }
    /**
     * @param string|object $class
     *
     * @return array
     */
    public function getModelEmbeddedLists($class)
    {
        return $this->getPreprocessorContext()->getModelEmbeddedLists($class);
    }
    /**
     * @param string|object $class
     *
     * @return bool
     */
    public function isModel($class)
    {
        return $this->getPreprocessorContext()->isModel($class);
    }
    /**
     * @param string|object $class
     *
     * @return array
     */
    public function getModelWorkflows($class)
    {
        return $this->getPreprocessorContext()->getModelWorkflows($class);
    }
    /**
     * @param string|object $class
     * @param string        $property
     *
     * @return array
     */
    public function getModelPropertyType($class, $property)
    {
        return $this->getPreprocessorContext()->getModelPropertyType($class, $property);
    }
    /**
     * @param string $type
     *
     * @return array
     */
    public function getEnumValuesByType($type)
    {
        return $this->getPreprocessorContext()->getEnumValuesByType($type);
    }
    /**
     * @param string|object $class
     * @param string        $property
     *
     * @return array
     *
     * @throws Exception
     */
    public function getModelHashListByProperty($class, $property)
    {
        return $this->getPreprocessorContext()->getModelHashListByProperty($class, $property);
    }
    /**
     * @param string|object $class
     *
     * @return array
     *
     * @throws Exception
     */
    public function getModelExposeRestricts($class)
    {
        return $this->getPreprocessorContext()->getModelExposeRestricts($class);
    }
    /**
     * @param string|object $class
     * @param string        $property
     *
     * @return array
     *
     * @throws Exception
     */
    public function getModelEmbeddedListByProperty($class, $property)
    {
        return $this->getPreprocessorContext()->getModelEmbeddedListByProperty($class, $property);
    }
    /**
     * @param string|object $class
     * @param string        $property
     *
     * @return array
     *
     * @throws Exception
     */
    public function getModelEmbeddedByProperty($class, $property)
    {
        return $this->getPreprocessorContext()->getModelEmbeddedByProperty($class, $property);
    }
    /**
     * @param string|object $class
     *
     * @return array
     */
    public function getModelGenerateds($class)
    {
        return $this->getPreprocessorContext()->getModelGenerateds($class);
    }
    /**
     * @param string|object $class
     *
     * @return array
     */
    public function getModelDefaults($class)
    {
        return $this->getPreprocessorContext()->getModelDefaults($class);
    }
    /**
     * @param string|object $class
     *
     * @return array
     */
    public function getModelStorages($class)
    {
        return $this->getPreprocessorContext()->getModelStorages($class);
    }
    /**
     * @param object $model
     * @param string $operation
     *
     * @return array
     */
    public function getModelRefreshablePropertiesByOperation($model, $operation)
    {
        return $this->getPreprocessorContext()->getModelRefreshablePropertiesByOperation($model, $operation);
    }
    /**
     * @param string|object $doc
     *
     * @return array
     */
    public function fetchModelDefinition($doc)
    {
        return $this->getPreprocessorContext()->fetchModelDefinition($doc);
    }
    /**
     * @param string|object $class
     *
     * @return array
     */
    public function getModelFingerPrints($class)
    {
        return $this->getPreprocessorContext()->getModelFingerPrints($class);
    }
    /**
     * @param string|object $class
     * @param string        $property
     *
     * @return array
     */
    public function getModelPropertySecures($class, $property)
    {
        return $this->getPreprocessorContext()->getModelPropertySecures($class, $property);
    }
    /**
     * @param string|object $class
     * @param array         $options
     *
     * @return array
     */
    public function getModelTriggers($class, array $options = [])
    {
        return $this->getPreprocessorContext()->getModelTriggers($class, $options);
    }
    /**
     * @param string|object $class
     *
     * @return array
     */
    public function getModelTypes($class)
    {
        return $this->getPreprocessorContext()->getModelTypes($class);
    }
    /**
     * @param string|object $class
     *
     * @return array
     */
    public function getModelWitnesses($class)
    {
        return $this->getPreprocessorContext()->getModelWitnesses($class);
    }
}
