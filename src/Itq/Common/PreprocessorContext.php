<?php

/*
 * This file is part of the COMMON package.
 *
 * (c) itiQiti SAS <opensource@itiqiti.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Itq\Common;

use ReflectionClass;
use ReflectionMethod;
use Itq\Common\Traits;
use ReflectionProperty;

/**
 * @author itiQiti Dev Team <opensource@itiqiti.com>
 */
class PreprocessorContext
{
    use Traits\ServiceTrait;
    /**
     * @var double
     */
    public $startTime;
    /**
     * @var double
     */
    public $endTime;
    /**
     * @var double
     */
    public $duration;
    /**
     * @var double
     */
    public $memory;
    /**
     * @var array
     */
    public $modelIds;
    /**
     * @var string
     */
    public $class;
    /**
     * @var string
     */
    public $property;
    /**
     * @var array
     */
    public $models;
    /**
     * @var array
     */
    public $preModels;
    /**
     * @var array
     */
    public $enums;
    /**
     * @var array
     */
    public $trackers;
    /**
     * @var array
     */
    public $retrievableStorages;
    /**
     * @var array
     */
    public $sdk;
    /**
     * @var string
     */
    public $method;
    /**
     * @var ReflectionMethod
     */
    public $rMethod;
    /**
     * @var ReflectionClass
     */
    public $rClass;
    /**
     * @var ReflectionProperty
     */
    public $rProperty;
    /**
     * @var array
     */
    public $classes;
    /**
     * @var string
     */
    public $cacheDir;
    /**
     * @var array
     *
     * This property is not archived/saved to cache file
     */
    private $unsaved;
    /**
     * @param array $data
     */
    public function __construct(array $data = [])
    {
        $this->memory                         = memory_get_usage(true);
        $this->startTime                      = microtime(true);
        $this->classes                        = [];
        $this->models                         = [];
        $this->preModels                      = [];
        $this->modelIds                       = [];
        $this->enums                          = [];
        $this->trackers                       = [];
        $this->retrievableStorages            = [];
        $this->sdk                            = ['targets' => []];
        $this->unsaved                        = ['containerServiceMethodCalls' => []];

        unset($data['unsaved']);

        foreach ($data as $k => $v) {
            $this->$k = $v;
        }
    }
    /**
     * @param string $id
     *
     * @return string
     *
     * @throws \Exception
     */
    public function getModelClassForId($id)
    {
        if (!isset($this->modelIds[strtolower($id)])) {
            throw $this->createRequiredException("Unknown model '%s'", $id);
        }

        return $this->modelIds[strtolower($id)];
    }
    /**
     * @param mixed $class
     *
     * @return string
     *
     * @throws \Exception
     */
    public function getModelIdForClass($class)
    {
        return $this->getModel($class)['id'];
    }
    /**
     * @param string|object $class
     *
     * @return bool
     */
    public function isModel($class)
    {
        return true === isset($this->models[is_object($class) ? get_class($class) : $class]);
    }
    /**
     * @param string|object $class
     *
     * @return $this
     *
     * @throws \Exception
     */
    public function checkModel($class)
    {
        if (!$this->isModel($class)) {
            throw $this->createUnexpectedException("Class '%s' is not registered as a model", $class);
        }

        return $this;
    }
    /**
     * @param string $type
     *
     * @return array
     */
    public function getEnumValuesByType($type)
    {
        return array_keys(isset($this->enums[$type]) ? $this->enums[$type] : []);
    }
    /**
     * @param string $operation
     *
     * @return array
     */
    public function getOperationTrackers($operation)
    {
        return isset($this->trackers[$operation]) ? $this->trackers[$operation] : [];
    }
    /**
     * @param string $class
     * @param string $property
     * @param array  $definition
     *
     * @return $this
     */
    public function addModelPropertyEmbeddedReference($class, $property, $definition)
    {
        $this->checkModel($class);

        $this->models[$class]['embeddedReferences'][$property] = $definition;

        $this->setModelPropertyModelType($class, $property, 'embeddedReference');

        $owningSideClass = $this->getModelClassForId($definition['type']);

        if (!isset($this->models[$owningSideClass])) {
            if (!isset($this->preModels[$owningSideClass])) {
                $this->preModels[$owningSideClass] = [];
            }
            if (!isset($this->preModels[$owningSideClass]['embeddedReferenceLinks'])) {
                $this->preModels[$owningSideClass]['embeddedReferenceLinks'] = [];
            }
            $l = &$this->preModels[$owningSideClass]['embeddedReferenceLinks'];
        } else {
            $l = &$this->models[$owningSideClass]['embeddedReferenceLinks'];
        }

        $joinClass  = $this->getModelClassForId($definition['localType']);
        $joinObject = new $joinClass();
        $fields     = array_keys(get_object_vars($joinObject));

        $l[] = [
            'owningSideType'  => $this->getModelIdForClass($class),
            'owningSideField' => $property,
            'joinClass'       => $joinClass,
            'fields'          => $fields,
            'key'             => isset($definition['key']) ? $definition['key'] : null,
        ];

        return $this;
    }
    /**
     * @param string $class
     * @param string $property
     * @param string $modelType
     *
     * @return $this
     */
    public function setModelPropertyModelType($class, $property, $modelType)
    {
        $this->checkModel($class);

        if (!isset($this->models[$class]['types'][$property])) {
            $this->models[$class]['types'][$property] = [];
        }

        $this->models[$class]['types'][$property]['modelType'] = $modelType;

        return $this;
    }
    /**
     * @param string $class
     * @param string $property
     * @param array  $definition
     *
     * @return $this
     */
    public function addModelPropertyVirtual($class, $property, $definition)
    {
        $this->checkModel($class);

        $this->models[$class]['virtuals'][$property] = $definition;

        return $this;
    }
    /**
     * @param string $class
     * @param string $property
     * @param array  $definition
     *
     * @return $this
     */
    public function addModelPropertyWitness($class, $property, $definition)
    {
        $this->checkModel($class);

        if (!isset($this->models[$class]['witnesses'][$definition['of']])) {
            $this->models[$class]['witnesses'][$definition['of']] = [];
        }

        $this->models[$class]['witnesses'][$definition['of']][] = ['property' => $property] + $definition;

        $this->setModelPropertyModelType($class, $property, 'witness');

        return $this;
    }
    /**
     * @param string $class
     * @param string $property
     * @param array  $definition
     *
     * @return $this
     */
    public function addModelPropertyRequirement($class, $property, $definition)
    {
        $this->checkModel($class);

        if (!isset($this->models[$class]['requirements'][$property])) {
            $this->models[$class]['requirements'][$property] = [
                'fields' => [],
            ];
        }

        if (isset($definition['fields']) && is_array($definition['fields']) && count($definition['fields'])) {
            $this->models[$class]['requirements'][$property]['fields'] = array_unique(
                array_merge(
                    $this->models[$class]['requirements'][$property]['fields'],
                    $definition['fields']
                )
            );
        }

        return $this;
    }
    /**
     * @param string $class
     * @param string $property
     * @param array  $definition
     *
     * @return $this
     *
     * @throws \Exception
     */
    public function addModelPropertyStorageUrl($class, $property, $definition)
    {
        $this->checkModel($class);

        if (isset($this->models[$class]['storageUrls'][$property])) {
            throw $this->createDuplicatedException("A storage url as already been registered on property '%s' of class '%s'", $property, is_object($class) ? get_class($class) : $class);
        }
        if (!is_array($definition)) {
            $definition = [];
        }

        $definition += ['alias' => false];

        if (!isset($definition['of'])) {
            $definition['of'] = preg_replace('/Url$/', '', $property);
        }
        if (!isset($definition['vars']) || !is_array($definition['vars'])) {
            $definition['vars'] = [];
        }
        if (!count($definition['vars'])) {
            $definition['vars'] = [
                'token' => '@token',
                'fingerPrint' => '@'.$definition['of'].'FingerPrint',
            ];
        }
        if (!isset($definition['vars']['level'])) {
            $definition['vars']['level'] = isset($definition['level'])
                ? $definition['level']
                : substr_count($this->models[$class]['id'], '.') + 1
            ;
        }
        if (isset($definition['prefix'])) {
            $definition['vars']['prefix'] = $definition['prefix'];
            if (true !== $definition['alias']) {
                if (isset($this->retrievableStorages[$definition['prefix']])) {
                    throw $this->createDuplicatedException(
                        sprintf(
                            "Retrieve prefix '%s' already registered for %s:%s",
                            $definition['prefix'],
                            $this->retrievableStorages[$definition['prefix']]['model'],
                            $this->retrievableStorages[$definition['prefix']]['property']
                        )
                    );
                }
                $this->retrievableStorages[$definition['prefix']] = [
                    'model'    => $this->getModel($class)['id'],
                    'property' => $definition['of'],
                    'sensitive' => isset($definition['sensitive']) ? (bool) $definition['sensitive'] : null,
                    'cacheTtl' => (isset($definition['cacheTtl']) && ((int) $definition['cacheTtl']) >= 0) ? (int) $definition['cacheTtl'] : null,
                ];
            }
        }

        $this->models[$class]['storageUrls'][$property] = $definition;

        if (isset($definition['vars']) && is_array($definition['vars']) && count($definition['vars'])) {
            $requiredFields = array_values($definition['vars']);
            foreach ($requiredFields as $k => $v) {
                if ('@' === substr($v, 0, 1)) {
                    $requiredFields[$k] = substr($v, 1);
                } else {
                    unset($requiredFields[$k]);
                }
            }
            $this->addModelPropertyRequirement($class, $property, ['fields' => $requiredFields]);
        }

        return $this;
    }
    /**
     * @param string $class
     * @param string $property
     * @param array  $definition
     *
     * @return $this
     *
     * @throws \Exception
     */
    public function addModelPropertyDynamicUrl($class, $property, $definition)
    {
        $modelId = $this->getModel($class)['id'];

        if (isset($this->models[$class]['dynamicUrls'][$property])) {
            throw $this->createDuplicatedException("A dynamic url as already been registered on property '%s' of class '%s'", $property, is_object($class) ? get_class($class) : $class);
        }
        if (!is_array($definition)) {
            $definition = [];
        }
        if (!isset($definition['vars']) || !is_array($definition['vars'])) {
            $definition['vars'] = [];
        }
        if (!isset($definition['fields']) || !is_array($definition['fields']) || !count($definition['fields'])) {
            $definition['fields'] = [];
        }
        if (!count($definition['vars'])) {
            foreach ($definition['fields'] as $k) {
                $definition['vars'][$k] = '@'.$k;
            }
        }

        $typePrefix = null;

        if (in_array('tenant', $definition['fields'])) {
            $typePrefix = sprintf('%s_', '{tenant}');
        }
        if (in_array('type', $definition['fields'])) {
            $typePrefix .= sprintf('%s_', '{type}');
        }
        if (!isset($definition['type'])) {
            $definition['type'] = sprintf('%s_%s', $modelId, preg_replace('/Url$/', '', $property));
        }
        if (isset($typePrefix)) {
            $definition['type'] = $typePrefix.$definition['type'];
        }

        $this->models[$class]['dynamicUrls'][$property] = $definition;

        if (isset($definition['vars']) && is_array($definition['vars']) && count($definition['vars'])) {
            $requiredFields = array_values($definition['vars']);
            foreach ($requiredFields as $k => $v) {
                if ('@' === substr($v, 0, 1)) {
                    $requiredFields[$k] = substr($v, 1);
                } else {
                    unset($requiredFields[$k]);
                }
            }
            $this->addModelPropertyRequirement($class, $property, ['fields' => $requiredFields]);
        }

        return $this;
    }
    /**
     * @param string $class
     * @param string $property
     * @param array  $definition
     *
     * @return $this
     */
    public function addModelPropertyReference($class, $property, $definition)
    {
        $this->checkModel($class);

        $this->models[$class]['references'][$property] = $definition;

        $this->setModelPropertyModelType($class, $property, 'reference');

        return $this;
    }
    /**
     * @param string $class
     * @param string $property
     * @param array  $definition
     *
     * @return $this
     */
    public function addModelPropertySecure($class, $property, $definition)
    {
        $this->checkModel($class);

        if (!is_array($definition)) {
            $definition = [];
        }

        $definition += ['operation' => 'all', 'role' => null, 'allow' => true, 'silent' => true, 'message' => null];

        $definition['operations'] = isset($definition['operation']) ? array_fill_keys(is_array($definition['operation']) ? $definition['operation'] : [$definition['operation']], true) : [];
        $definition['roles'] = isset($definition['role']) ? array_fill_keys(
            array_map(
                function ($r) {
                    return 'ROLE_'.strtoupper($r);
                },
                is_array($definition['role']) ? $definition['role'] : [$definition['role']]
            ),
            true
        ) : [];
        unset($definition['operation'], $definition['role']);
        $this->models[$class]['secures'][$property] = $definition;

        return $this;
    }
    /**
     * @param string $class
     * @param string $property
     * @param array  $definition
     *
     * @return $this
     */
    public function addModelPropertyExposeRestrict($class, $property, $definition)
    {
        $this->checkModel($class);

        if (!is_array($definition)) {
            $definition = [];
        }

        $definition += ['roles' => []];

        if (!isset($this->models[$class]['exposeRestricts'][$property])) {
            $this->models[$class]['exposeRestricts'][$property] = [];
        }

        $this->models[$class]['exposeRestricts'][$property][] = $definition;

        return $this;
    }
    /**
     * @param string $class
     * @param string $property
     * @param array  $definition
     *
     * @return $this
     */
    public function addModelPropertyEmbedded($class, $property, $definition)
    {
        $this->checkModel($class);

        $definition['class'] = $this->getModelClassForId($definition['type']);

        $this->models[$class]['embeddeds'][$property] = $definition;

        $this->setModelPropertyModelType($class, $property, 'embedded');

        return $this;
    }
    /**
     * @param string $class
     * @param string $property
     * @param array  $definition
     *
     * @return $this
     */
    public function addModelPropertyBasicList($class, $property, $definition)
    {
        $this->checkModel($class);

        $this->models[$class]['basicLists'][$property] = $definition;

        $this->setModelPropertyModelType($class, $property, 'basicList');

        return $this;
    }
    /**
     * @param string $class
     * @param string $property
     * @param array  $definition
     *
     * @return $this
     */
    public function addModelPropertyTagList($class, $property, $definition)
    {
        $this->checkModel($class);

        $this->models[$class]['tagLists'][$property] = $definition;

        $this->setModelPropertyModelType($class, $property, 'tagList');
        if (!isset($this->models[$class]['updateEnrichments'][$property])) {
            $this->models[$class]['updateEnrichments'][$property] = [];
        }
        $this->models[$class]['updateEnrichments'][$property][] = ['type' => 'toggleItems'];

        return $this;
    }
    /**
     * @param string $class
     * @param string $property
     * @param array  $definition
     *
     * @return $this
     */
    public function addModelPropertyHashList($class, $property, $definition)
    {
        $this->checkModel($class);

        $this->models[$class]['hashLists'][$property] = $definition;

        $this->setModelPropertyModelType($class, $property, 'hashList');

        return $this;
    }
    /**
     * @param string $class
     * @param string $property
     * @param array  $definition
     *
     * @return $this
     */
    public function addModelPropertyVirtualEmbeddedReferenceList($class, $property, $definition)
    {
        $this->checkModel($class);

        $this->models[$class]['virtualEmbeddedReferenceLists'][$property] = $definition;

        $this->setModelPropertyModelType($class, $property, 'virtualEmbeddedReferenceList');

        return $this;
    }
    /**
     * @param string $class
     * @param string $property
     * @param array  $definition
     *
     * @return $this
     */
    public function addModelPropertyVirtualEmbeddedReference($class, $property, $definition)
    {
        $this->checkModel($class);

        $this->models[$class]['virtualEmbeddedReferences'][$property] = $definition;

        $this->setModelPropertyModelType($class, $property, 'virtualEmbeddedReference');

        return $this;
    }
    /**
     * @param string $class
     * @param string $property
     * @param array  $definition
     *
     * @return $this
     */
    public function addModelPropertyEmbeddedList($class, $property, $definition)
    {
        $this->checkModel($class);

        $this->models[$class]['embeddedLists'][$property] = $definition;

        $this->setModelPropertyModelType($class, $property, 'embeddedList');

        return $this;
    }
    /**
     * @param string $class
     * @param string $property
     * @param array  $definition
     *
     * @return $this
     */
    public function addModelPropertyCachedList($class, $property, $definition)
    {
        $this->checkModel($class);

        $this->models[$class]['cachedLists'][$property] = $definition;

        $modelId = $this->models[$class]['id'];

        foreach ($definition['triggers'] as $triggerName => $trigger) {
            $sourceClass = $this->getModelClassForId($trigger['model']);
            unset($trigger['model']);
            $this->checkModel($sourceClass);
            if (!isset($this->models[$sourceClass]['triggers'])) {
                $this->models[$sourceClass]['triggers'] = [];
            }
            $this->models[$sourceClass]['triggers'][$modelId.'.'.$triggerName] = ['targetDocType' => $modelId, 'targetDocProperty' => $property] + $trigger;
        }

        $this->setModelPropertyModelType($class, $property, 'cachedList');

        return $this;
    }
    /**
     * @param string $class
     * @param string $property
     * @param array  $definition
     *
     * @return $this
     */
    public function addModelPropertyRefresh($class, $property, $definition)
    {
        $this->checkModel($class);

        $operations = is_array($definition['value']) ? $definition['value'] : [$definition['value']];

        foreach ($operations as $operation) {
            $pp = strpos($operation, '.');
            $operationField = null;
            $operationFieldValue = null;
            if (false !== $pp) {
                $operationField = substr($operation, $pp + 1);
                $operation = substr($operation, 0, $pp);
                $dpp = strpos($operationField, ':');
                if (false !== $dpp) {
                    $operationFieldValue = substr($operationField, $dpp + 1);
                    $operationField = substr($operationField, 0, $dpp);
                }
            }
            $def = ['operationField' => $operationField, 'operationFieldValue' => $operationFieldValue];
            if (!isset($this->models[$class]['refreshes'][$operation])) {
                $this->models[$class]['refreshes'][$operation] = [];
            }

            $this->models[$class]['refreshes'][$operation][$property] = $def;
        }

        return $this;
    }
    /**
     * @param string $class
     * @param string $property
     * @param array  $definition
     *
     * @return $this
     */
    public function addModelPropertyEnum($class, $property, $definition)
    {
        $this->checkModel($class);

        $values = $definition['value'];
        if (!is_array($values)) {
            if ('@' !== substr($values, 0, 1)) {
                $values = [];
            }
        }

        if (!isset($this->models[$class]['types'][$property])) {
            $this->models[$class]['types'][$property] = [];
        }

        $this->models[$class]['types'][$property]['type'] = 'enum';
        $this->models[$class]['types'][$property]['values'] = $values;

        $this->setModelPropertyModelType($class, $property, 'enum');

        return $this;
    }
    /**
     * @param string $class
     * @param string $property
     * @param array  $definition
     *
     * @return $this
     */
    public function addModelPropertyGeoPoint($class, $property, $definition)
    {
        $this->checkModel($class);

        $name      = $definition['name'];
        $component = $definition['component'];

        if (!isset($this->models[$class]['geopoints'][$property])) {
            $this->models[$class]['geopoints'][$property] = [];
        }

        $this->models[$class]['geopoints'][$property][$name] = $definition;

        if (!isset($this->models[$class]['geopointVirtuals'][$name])) {
            $this->models[$class]['geopointVirtuals'][$name] = [];
        }

        $this->models[$class]['geopointVirtuals'][$name][$component] = $property;

        return $this;
    }
    /**
     * @param string $class
     * @param string $property
     * @param array  $definition
     *
     * @return $this
     */
    public function addModelPropertyDefaultValue($class, $property, $definition)
    {
        $this->checkModel($class);

        $value = isset($definition['value']) ? $definition['value'] : null;

        if (!isset($this->models[$class]['types'][$property])) {
            $this->models[$class]['types'][$property] = [];
        }

        $this->models[$class]['types'][$property]['default'] = ['value' => $value, 'generator' => isset($definition['generator']) ? $definition['generator'] : null, 'options' => isset($definition['options']) ? $definition['options'] : []];

        return $this;
    }
    /**
     * @param string $class
     * @param string $property
     * @param array  $definition
     *
     * @return $this
     */
    public function addModelPropertyGenerated($class, $property, $definition)
    {
        $this->checkModel($class);

        $definition['type'] = $definition['value'];
        unset($definition['value']);

        $this->models[$class]['generateds'][$property] = $definition;

        return $this;
    }
    /**
     * @param string $class
     * @param string $property
     * @param array  $definition
     *
     * @return $this
     */
    public function addModelPropertyFingerPrint($class, $property, $definition)
    {
        $this->checkModel($class);

        unset($definition['value']);

        if (!isset($definition['of'])) {
            $definition['of'] = [];
        }

        if (!is_array($definition['of'])) {
            $definition['of'] = [$definition['of']];
        }

        $this->models[$class]['fingerPrints'][$property] = $definition;

        return $this;
    }
    /**
     * @param string $class
     * @param string $property
     * @param array  $definition
     *
     * @return $this
     *
     * @throws \Exception
     */
    public function addModelPropertyStorage($class, $property, $definition)
    {
        $this->checkModel($class);

        $definition['key'] = $definition['value'];
        unset($definition['value']);

        $this->models[$class]['storages'][$property] = $definition;

        if (isset($definition['retrievePrefix'])) {
            if (isset($this->retrievableStorages[$definition['retrievePrefix']])) {
                throw $this->createDuplicatedException(sprintf("Retrieve prefix '%s' already registered for %s:%s", $definition['retrievePrefix'], $this->retrievableStorages[$definition['retrievePrefix']]['model'], $this->retrievableStorages[$definition['retrievePrefix']]['property']));
            }
            $this->retrievableStorages[$definition['retrievePrefix']] = ['model' => $this->getModel($class)['id'], 'property' => $property];
        }

        return $this;
    }
    /**
     * @param string|Object $class
     *
     * @return array
     */
    public function getModelTypes($class)
    {
        if (is_object($class)) {
            $class = get_class($class);
        }

        $this->checkModel($class);

        return $this->models[$class]['types'];
    }
    /**
     * @param string $prefix
     *
     * @return array
     *
     * @throws \Exception
     */
    public function getRetrievableStorageByPrefix($prefix)
    {
        if (!isset($this->retrievableStorages[$prefix])) {
            throw $this->createNotFoundException(sprintf("Retrievable Storage prefix '%s' not registered", $prefix));
        }

        return $this->retrievableStorages[$prefix];
    }
    /**
     * @param string $class
     * @param string $property
     * @param array  $definition
     *
     * @return $this
     */
    public function addModelPropertyWorkflow($class, $property, $definition)
    {
        $this->checkModel($class);

        $this->models[$class]['workflows'][$property] = $definition;

        if (!isset($this->models[$class]['types'][$property])) {
            $this->models[$class]['types'][$property] = [];
        }

        $this->models[$class]['types'][$property]['type'] = 'workflow';
        $this->models[$class]['types'][$property]['values'] = isset($definition['steps']) ? $definition['steps'] : [];
        $this->models[$class]['types'][$property]['steps'] = isset($definition['steps']) ? $definition['steps'] : [];
        $this->models[$class]['types'][$property]['transitions'] = isset($definition['transitions']) ? $definition['transitions'] : [];

        $this->setModelPropertyModelType($class, $property, 'workflow');

        return $this;
    }
    /**
     * @param string $class
     * @param string $property
     * @param array  $definition
     *
     * @return $this
     */
    public function addModelPropertyId($class, $property, $definition)
    {
        $this->checkModel($class);

        $definition['name'] = isset($definition['name']) ? $definition['name'] : '_id';
        $definition['property'] = $property;
        unset($definition['value']);

        $this->models[$class]['ids'] = $definition;

        $this->setModelPropertyModelType($class, $property, 'id');

        return $this;
    }
    /**
     * @param string $class
     * @param string $property
     * @param array  $definition
     *
     * @return $this
     */
    public function setModelPropertyType($class, $property, $definition)
    {
        $this->checkModel($class);

        if (!isset($this->models[$class]['types'][$property])) {
            $this->models[$class]['types'][$property] = [];
        }

        if (!isset($this->models[$class]['types'][$property]['type'])) {
            $this->models[$class]['types'][$property]['type'] = $definition['name'];
        }

        return $this;
    }
    /**
     * @param string $target
     * @param string $sourceClass
     * @param string $sourceMethod
     * @param string $route
     * @param string $service
     * @param string $method
     * @param string $type
     * @param array  $params
     * @param array  $return
     * @param array  $options
     *
     * @return $this
     *
     * @throws \Exception
     */
    public function addSdkMethod($target, $sourceClass, $sourceMethod, $route, $service, $method, $type, $params = [], $return = [], $options = []) // done
    {
        if (!isset($this->sdk['targets'][$target]['services'][$service])) {
            if (!isset($this->sdk['targets'][$target])) {
                $this->sdk['targets'][$target] = ['services' => []];
            }
            $this->sdk['targets'][$target]['services'][$service] = ['methods' => []];
        }

        if (isset($this->sdk['targets'][$target]['services'][$service]['methods'][$method])) {
            throw $this->createDuplicatedException("SDK Method '%s' already registered for service '%s'", $method, $service);
        }

        $this->sdk['targets'][$target]['services'][$service]['methods'][$method] = [
            'sourceClass'  => $sourceClass,
            'sourceMethod' => $sourceMethod,
            'type'         => $type,
            'route'        => $route,
            'params'       => $params,
            'return'       => $return,
            'options'      => $options,
        ];

        return $this;
    }
    /**
     * @param string|Object $class
     *
     * @return array
     */
    public function getModelEmbeddedReferences($class)
    {
        if (is_object($class)) {
            $class = get_class($class);
        }

        $this->checkModel($class);

        return $this->models[$class]['embeddedReferences'];
    }
    /**
     * @param string|Object $class
     * @param array         $options
     *
     * @return array
     */
    public function getModelEmbeddedReferenceLinks($class, array $options = [])
    {
        if (is_object($class)) {
            $class = get_class($class);
        }

        $this->checkModel($class);

        unset($options);

        return $this->models[$class]['embeddedReferenceLinks'];
    }
    /**
     * @param string|Object $class
     *
     * @return array
     */
    public function getModelWitnesses($class)
    {
        if (is_object($class)) {
            $class = get_class($class);
        }

        $this->checkModel($class);

        return $this->models[$class]['witnesses'];
    }
    /**
     * @param string|Object $class
     * @param string        $property
     *
     * @return array
     */
    public function getModelPropertyRequirements($class, $property)
    {
        if (is_object($class)) {
            $class = get_class($class);
        }

        $this->checkModel($class);

        return isset($this->models[$class]['requirements'][$property]) ? $this->models[$class]['requirements'][$property] : ['fields' => []];
    }
    /**
     * @param string|Object $class
     *
     * @return array
     */
    public function getModelStorageUrls($class)
    {
        if (is_object($class)) {
            $class = get_class($class);
        }

        $this->checkModel($class);

        return $this->models[$class]['storageUrls'];
    }
    /**
     * @param string|Object $class
     *
     * @return array
     */
    public function getModelDynamicUrls($class)
    {
        if (is_object($class)) {
            $class = get_class($class);
        }

        $this->checkModel($class);

        return $this->models[$class]['dynamicUrls'];
    }
    /**
     * @param string|Object $class
     * @param string        $property
     *
     * @return array
     *
     * @throws \Exception
     */
    public function getModelPropertyDynamicUrl($class, $property)
    {
        if (is_object($class)) {
            $class = get_class($class);
        }

        $this->checkModel($class);

        if (!isset($this->models[$class]['dynamicUrls'][$property])) {
            throw $this->createRequiredException(
                "No dynamic url registered for property '%s' on class '%s'",
                $property,
                is_object($class) ? get_class($class) : $class
            );
        }

        return $this->models[$class]['dynamicUrls'][$property];
    }
    /**
     * @param string|Object $class
     * @param string        $property
     *
     * @return array
     *
     * @throws \Exception
     */
    public function getModelPropertySecures($class, $property)
    {
        if (is_object($class)) {
            $class = get_class($class);
        }

        $this->checkModel($class);

        if (!isset($this->models[$class]['secures'][$property])) {
            return [];
        }

        return $this->models[$class]['secures'][$property];
    }
    /**
     * @param string|Object $class
     *
     * @return array
     */
    public function getModelRestricts($class)
    {
        if (is_object($class)) {
            $class = get_class($class);
        }

        $this->checkModel($class);

        return $this->models[$class]['restricts'];
    }
    /**
     * @param string|Object $class
     *
     * @return array
     */
    public function getModelExposeRestricts($class)
    {
        if (is_object($class)) {
            $class = get_class($class);
        }

        $this->checkModel($class);

        return $this->models[$class]['exposeRestricts'];
    }
    /**
     * @param string|Object $class
     *
     * @return array
     */
    public function getModelReferences($class)
    {
        if (is_object($class)) {
            $class = get_class($class);
        }

        $this->checkModel($class);

        return $this->models[$class]['references'];
    }
    /**
     * @param string|Object $class
     *
     * @return array
     */
    public function getModelEmbeddeds($class)
    {
        if (is_object($class)) {
            $class = get_class($class);
        }

        $this->checkModel($class);

        return $this->models[$class]['embeddeds'];
    }
    /**
     * @param string|Object $class
     *
     * @return array
     */
    public function getModelHashLists($class)
    {
        if (is_object($class)) {
            $class = get_class($class);
        }

        $this->checkModel($class);

        return $this->models[$class]['hashLists'];
    }
    /**
     * @param string|Object $class
     *
     * @return array
     */
    public function getModelBasicLists($class)
    {
        if (is_object($class)) {
            $class = get_class($class);
        }

        $this->checkModel($class);

        return $this->models[$class]['basicLists'];
    }
    /**
     * @param string|Object $class
     *
     * @return array
     */
    public function getModelTagLists($class)
    {
        if (is_object($class)) {
            $class = get_class($class);
        }

        $this->checkModel($class);

        return $this->models[$class]['tagLists'];
    }
    /**
     * @param string|Object $class
     *
     * @return array
     */
    public function getModelUpdateEnrichments($class)
    {
        if (is_object($class)) {
            $class = get_class($class);
        }

        $this->checkModel($class);

        return $this->models[$class]['updateEnrichments'];
    }
    /**
     * @param string|Object $class
     * @param array         $options
     *
     * @return array
     */
    public function getModelTriggers($class, array $options = [])
    {
        if (is_object($class)) {
            $class = get_class($class);
        }

        $this->checkModel($class);

        unset($options);

        return $this->models[$class]['triggers'];
    }
    /**
     * @param string|Object $class
     * @param string        $property
     *
     * @return array
     *
     * @throws \Exception
     */
    public function getModelHashListByProperty($class, $property)
    {
        if (is_object($class)) {
            $class = get_class($class);
        }

        $this->checkModel($class);

        if (!isset($this->models[$class]['hashLists'][$property])) {
            throw $this->createRequiredException("Property '%s' is a not a hash list", $property);
        }

        return $this->models[$class]['hashLists'][$property];
    }
    /**
     * @param string|Object $class
     * @param string        $property
     *
     * @return array
     *
     * @throws \Exception
     */
    public function getModelEmbeddedListByProperty($class, $property)
    {
        if (is_object($class)) {
            $class = get_class($class);
        }

        $this->checkModel($class);

        if (!isset($this->models[$class]['embeddedLists'][$property])) {
            throw $this->createRequiredException("Property '%s' is a not an embedded list", $property);
        }

        return $this->models[$class]['embeddedLists'][$property];
    }
    /**
     * @param string|Object $class
     * @param string        $property
     *
     * @return array
     *
     * @throws \Exception
     */
    public function getModelEmbeddedByProperty($class, $property)
    {
        if (is_object($class)) {
            $class = get_class($class);
        }

        $this->checkModel($class);

        if (!isset($this->models[$class]['embeddeds'][$property])) {
            throw $this->createRequiredException("Property '%s' is a not an embedded", $property);
        }

        return $this->models[$class]['embeddeds'][$property];
    }
    /**
     * @param string $target
     *
     * @return array
     *
     * @throws \Exception
     */
    public function getSdkServices($target)
    {
        if (!isset($this->sdk['targets'][$target]['services'])) {
            throw $this->createRequiredException("Unknown SDK target '%s'", $target);
        }

        return $this->sdk['targets'][$target]['services'];
    }
    /**
     * @param string|Object $class
     *
     * @return array
     */
    public function getModelDefaults($class)
    {
        if (is_object($class)) {
            $class = get_class($class);
        }

        $this->checkModel($class);

        $defaults = [];

        foreach ($this->models[$class]['types'] as $property => $type) {
            if (!isset($type['default'])) {
                continue;
            }
            $defaults[$property] = $type['default'];
        }

        return $defaults;
    }
    /**
     * @param string|Object $class
     *
     * @return array
     */
    public function getModelGenerateds($class)
    {
        return $this->getExistingModelMetaData($class, 'generateds', []);
    }
    /**
     * @param string|Object $class
     *
     * @return array
     */
    public function getModelFingerPrints($class)
    {
        return $this->getExistingModelMetaData($class, 'fingerPrints', []);
    }
    /**
     * @param string|Object $class
     *
     * @return array
     */
    public function getModelStorages($class)
    {
        return $this->getExistingModelMetaData($class, 'storages', []);
    }
    /**
     * @param string|Object $class
     *
     * @return array
     */
    public function getModelWorkflows($class)
    {
        return $this->getExistingModelMetaData($class, 'workflows', []);
    }
    /**
     * @param string|Object $class
     *
     * @return array
     */
    public function getModelVirtualEmbeddedReferenceLists($class)
    {
        return $this->getExistingModelMetaData($class, 'virtualEmbeddedReferenceLists', []);
    }
    /**
     * @param string|Object $class
     *
     * @return array
     */
    public function getModelVirtualEmbeddedReferences($class)
    {
        return $this->getExistingModelMetaData($class, 'virtualEmbeddedReferences', []);
    }
    /**
     * @param string|Object $class
     *
     * @return array
     */
    public function getModelEmbeddedLists($class)
    {
        return $this->getExistingModelMetaData($class, 'embeddedLists', []);
    }
    /**
     * @param string|Object $class
     *
     * @return array
     */
    public function getModelCachedLists($class)
    {
        return $this->getExistingModelMetaData($class, 'cachedLists', []);
    }
    /**
     * @param string|Object $class
     *
     * @return array
     */
    public function getModelVirtuals($class)
    {
        return $this->getExistingModelMetaData($class, 'virtuals', []);
    }
    /**
     * @param string|Object $class
     *
     * @return array|null
     */
    public function getModelIdProperty($class)
    {
        return $this->getExistingModelMetaData($class, 'ids', []);
    }
    /**
     * @param Object $model
     * @param string $operation
     *
     * @return array
     */
    public function getModelRefreshablePropertiesByOperation($model, $operation)
    {
        $this->checkModel($model);

        $class = get_class($model);

        $refreshes = isset($this->models[$class]['refreshes'][$operation])
            ? $this->models[$class]['refreshes'][$operation]
            : []
        ;

        foreach ($refreshes as $k => $v) {
            if (!isset($v['operationField'])) {
                continue;
            }
            if (!isset($model->{$v['operationField']})) {
                unset($refreshes[$k]);
                continue;
            }
            if (!isset($v['operationFieldValue'])) {
                continue;
            }
            if ('false' === $v['operationFieldValue']) {
                $v['operationFieldValue'] = false;
            } elseif ('true' === $v['operationFieldValue']) {
                $v['operationFieldValue'] = true;
            }

            if ($v['operationFieldValue'] !== $model->{$v['operationField']}) {
                unset($refreshes[$k]);
                continue;
            }
        }

        return array_keys($refreshes);
    }
    /**
     * @param string|Object $class
     * @param string        $property
     *
     * @return array
     */
    public function getModelPropertyType($class, $property)
    {
        if (is_object($class)) {
            $class = get_class($class);
        }

        $this->checkModel($class);

        return isset($this->models[$class]['types'][$property])
            ? $this->models[$class]['types'][$property]
            : null;
    }
    /**
     * @param string|Object $class
     *
     * @return null|string
     */
    public function getModelPropertyTypes($class)
    {
        return $this->getExistingModelMetaData($class, 'types', []);
    }
    /**
     * @param string|Object $class
     *
     * @return array
     */
    public function getModel($class)
    {
        if (is_object($class)) {
            $class = get_class($class);
        }

        if ('stdClass' === $class) {
            // bypass check if stdClass (used for array to object cast)
            return [];
        }
        $this->checkModel($class);

        return $this->models[$class];
    }
    /**
     * @param mixed $doc
     *
     * @return array
     */
    public function fetchModelDefinition($doc)
    {
        return [
            'types'                         => $this->getModelTypes($doc),
            'virtuals'                      => $this->getModelVirtuals($doc),
            'tagLists'                      => $this->getModelTagLists($doc),
            'hashLists'                     => $this->getModelHashLists($doc),
            'embeddeds'                     => $this->getModelEmbeddeds($doc),
            'basicLists'                    => $this->getModelBasicLists($doc),
            'cachedLists'                   => $this->getModelCachedLists($doc),
            'storageUrls'                   => $this->getModelStorageUrls($doc),
            'dynamicUrls'                   => $this->getModelDynamicUrls($doc),
            'embeddedLists'                 => $this->getModelEmbeddedLists($doc),
            'embeddedReferences'            => $this->getModelEmbeddedReferences($doc),
            'virtualEmbeddedReferences'     => $this->getModelVirtualEmbeddedReferences($doc),
            'virtualEmbeddedReferenceLists' => $this->getModelVirtualEmbeddedReferenceLists($doc),
        ];
    }
    /**
     * @param string $class
     * @param array  $definition
     *
     * @return $this
     */
    public function addModel($class, array $definition)
    {
        $definition['id'] = isset($definition['id']) ? $definition['id'] : $definition['value'];
        unset($definition['value']);

        if (!isset($this->models)) {
            $this->models = [];
        }
        $this->models[$class] = [];

        if (isset($this->preModels[$class])) {
            $this->models[$class] = $this->preModels[$class];
            unset($this->preModels[$class]);
        }

        $this->models[$class] += [
            'hashLists'                     => [],
            'basicLists'                    => [],
            'embeddeds'                     => [],
            'embeddedLists'                 => [],
            'embeddedReferences'            => [],
            'virtualEmbeddedReferenceLists' => [],
            'virtualEmbeddedReferences'     => [],
            'references'                    => [],
            'refreshes'                     => [],
            'generateds'                    => [],
            'storages'                      => [],
            'ids'                           => [],
            'types'                         => [],
            'fingerPrints'                  => [],
            'workflows'                     => [],
            'triggers'                      => [],
            'cachedLists'                   => [],
            'updateEnrichments'             => [],
            'tagLists'                      => [],
            'restricts'                     => [],
            'witnesses'                     => [],
            'virtuals'                      => [],
            'embeddedReferenceLinks'        => [],
            'requirements'                  => [],
            'storageUrls'                   => [],
            'dynamicUrls'                   => [],
            'secures'                       => [],
            'geopoints'                     => [],
            'geopointVirtuals'              => [],
            'exposeRestricts'               => [],
        ];

        $this->models[$class] += $definition;
        $this->modelIds[strtolower($definition['id'])] = $this->class;

        return $this;
    }
    /**
     * @param string $class
     * @param array  $definition
     *
     * @throws \Exception
     */
    public function addModelRestrict($class, array $definition)
    {
        $definition['operation'] = isset($definition['operation']) ? $definition['operation'] : $definition['value'];
        unset($definition['value']);

        if (!isset($definition['operation'])) {
            throw $this->createRequiredException("Missing 'operation' for model (%s) restrict: %s", $class, json_encode($definition));
        }

        if (!isset($this->models[$this->class]['restricts'][$definition['operation']])) {
            $this->models[$class]['restricts'][$definition['operation']] = [];
        }

        $this->models[$class]['restricts'][$definition['operation']][] = $definition;
    }
    /**
     * @param string $class
     * @param array  $definition
     *
     * @throws \Exception
     */
    public function addModelStat($class, array $definition)
    {
        $definition['key'] = isset($definition['key']) ? $definition['key'] : $definition['value'];
        unset($definition['value']);

        if (!isset($definition['on'])) {
            throw $this->createRequiredException("Missing 'on' for model (%s) stat: %s", $class, json_encode($definition));
        }
        if (!is_array($definition['on'])) {
            $definition['on'] = [$definition['on']];
        }

        foreach ($definition['on'] as $on) {
            $def = $definition;
            unset($def['on']);
            $targetType = $this->models[$class]['id'];

            if (!isset($this->trackers[$on])) {
                $this->trackers[$on] = [];
            }
            if (!isset($this->trackers[$on]['stat'])) {
                $this->trackers[$on]['stat'] = [];
            }
            if (!isset($this->trackers[$on]['stat'][$targetType])) {
                $this->trackers[$on]['stat'][$targetType] = [];
            }

            $this->trackers[$on]['stat'][$targetType][] = $def;
        }
    }
    /**
     * @return array
     */
    public function getModels()
    {
        return $this->models;
    }
    /**
     * @param string $serviceId
     * @param string $methodName
     * @param array  $params
     * @param array  $options
     *
     * @return $this
     */
    public function registerContainerMethodCall($serviceId, $methodName, array $params = [], array $options = [])
    {
        if (!isset($this->unsaved['containerServiceMethodCalls'][$serviceId][$methodName])) {
            if (!isset($this->unsaved['containerServiceMethodCalls'][$serviceId])) {
                $this->unsaved['containerServiceMethodCalls'][$serviceId] = [];
            }
            $this->unsaved['containerServiceMethodCalls'][$serviceId][$methodName] = [];
        }
        $this->unsaved['containerServiceMethodCalls'][$serviceId][$methodName][] = [$params, $options];

        return $this;
    }
    /**
     * @return $this
     */
    public function prepareForSave()
    {
        $this->unsaved = [];

        return $this;
    }
    /**
     * @return array
     */
    public function getRegisteredContainerMethodCalls()
    {
        return $this->unsaved['containerServiceMethodCalls'];
    }
    /**
     * @param array $data
     *
     * @return $this
     */
    public static function __set_state(array $data)
    {
        return new self($data);
    }
    /**
     * @param string|Object $class
     * @param string        $metaData
     * @param mixed         $default
     *
     * @return array
     */
    protected function getExistingModelMetaData($class, $metaData, $default = null)
    {
        if (is_object($class)) {
            $class = get_class($class);
        }

        $this->checkModel($class);

        return isset($this->models[$class][$metaData]) ? $this->models[$class][$metaData] : $default;
    }
}
