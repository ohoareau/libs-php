<?php

/*
 * This file is part of the COMMON package.
 *
 * (c) itiQiti SAS <opensource@itiqiti.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Itq\Common\Traits;

/**
 * ModelServiceHelperTrait.
 *
 * @author itiQiti Dev Team <opensource@itiqiti.com>
 */
trait ModelServiceHelperTrait
{
    use ServiceTrait;
    use LoggerAwareTrait;
    use ServiceAware\FormServiceAwareTrait;
    use ServiceAware\ModelServiceAwareTrait;
    use ServiceAware\MetaDataServiceAwareTrait;
    use ServiceAware\WorkflowServiceAwareTrait;
    use ServiceAware\BusinessRuleServiceAwareTrait;
    /**
     * @return int|null
     */
    public abstract function getExpectedTypeCount();
    /**
     * @param array $types
     *
     * @return $this
     *
     * @throws \Exception
     */
    public function setTypes(array $types)
    {
        $expectedTypeCount = $this->getExpectedTypeCount();

        if (null !== $expectedTypeCount && $expectedTypeCount !== count($types)) {
            throw $this->createUnexpectedException(
                "Model service must have exactly %d types (found: %d)",
                $expectedTypeCount,
                count($types)
            );
        }

        return $this->setParameter('types', $types);
    }
    /**
     * @return array
     */
    public function getTypes()
    {
        return $this->getParameter('types');
    }
    /**
     * @param string $separator
     *
     * @return string
     */
    public function getFullType($separator = '.')
    {
        return join($separator, $this->getTypes());
    }
    /**
     * Test if specified document event has registered event listeners.
     *
     * @param string $event
     *
     * @return bool
     */
    protected function observed($event)
    {
        return $this->hasListeners($this->buildEventName($event));
    }
    /**
     * Build the full event name.
     *
     * @param string $event
     *
     * @return string
     */
    protected function buildEventName($event)
    {
        return join('.', $this->getTypes()).'.'.$event;
    }
    /**
     * @param mixed $bulkData
     * @param array $options
     *
     * @return $this
     *
     * @throws \Exception
     */
    protected function checkBulkData($bulkData, $options = [])
    {
        if (!is_array($bulkData)) {
            throw $this->createRequiredException('Missing bulk data');
        }

        if (!count($bulkData)) {
            throw $this->createRequiredException('No data to process');
        }

        unset($options);

        return $this;
    }
    /**
     * Return the underlying model class.
     *
     * @param string $alias
     *
     * @return string
     */
    protected function getModelClass($alias = null)
    {
        if (null !== $alias) {
            if ('.' === substr($alias, 0, 1)) {
                return $this->getModelClass().'\\'.substr($alias, 1);
            }

            return $alias;
        }

        return $this->getMetaDataService()->getModelClassForId(join('.', $this->getTypes()));
    }
    /**
     * Return a new instance of the model.
     *
     * @param array $options
     *
     * @return mixed
     */
    protected function createModelInstance($options = [])
    {
        if (isset($options['model']) && !is_bool($options['model'])) {
            if (is_object($options['model'])) {
                return $options['model'];
            }
            $class = $this->getModelClass($options['model']);
        } else {
            $class = $this->getModelClass();
        }

        return new $class();
    }
    /**
     * @param array $values
     * @param array $options
     *
     * @return array
     */
    protected function buildTypeVars($values, $options = [])
    {
        $vars = [];

        $options += ['suffix' => 'Id'];

        foreach ($this->getTypes() as $type) {
            if (!count($values)) {
                $value = null;
            } else {
                $value = array_shift($values);
            }
            $vars[$type.$options['suffix']] = $value;
        }

        return $vars;
    }
    /**
     * @param array  $data
     * @param string $mode
     * @param array  $options
     *
     * @return array
     */
    protected function getUnvalidableKeys(array $data, $mode, array $options)
    {
        $cleared = [];

        foreach ($data as $k => $v) {
            if (is_string($v) && false !== strpos($v, '*cleared*')) {
                $cleared[$k] = true;
            }
        }

        unset($mode);
        unset($options);

        return $cleared;
    }
    /**
     * @param string $mode
     * @param array  $data
     * @param array  $options
     *
     * @return mixed
     */
    protected function validateData(array $data = [], $mode = 'create', array $options = [])
    {
        return $this->getFormService()->validate($this->getFullType(), $mode, $data, $options + ['unvalidableKeys' => $this->getUnvalidableKeys($data, $mode, $options)]);
    }
    /**
     * @param mixed $model
     * @param array $options
     *
     * @return mixed
     */
    protected function refreshModel($model, array $options = [])
    {
        return $this->getModelService()->refresh($model, $options);
    }
    /**
     * @param array  $data
     * @param string $class
     * @param array  $options
     *
     * @return array
     */
    protected function enrichUpdates($data, $class, array $options = [])
    {
        return $this->getModelService()->enrichUpdates($data, $class, $options);
    }
    /**
     * @param mixed $model
     * @param array $options
     *
     * @return mixed
     */
    protected function cleanModel($model, array $options = [])
    {
        return $this->getModelService()->clean($model, $options);
    }
    /**
     * Convert provided model (object) to an array.
     *
     * @param mixed $model
     * @param array $options
     *
     * @return array
     */
    protected function convertToArray($model, array $options = [])
    {
        return $this->getModelService()->convertObjectToArray($model, $options);
    }
    /**
     * Convert provided data (array) to a model.
     *
     * @param array $data
     * @param array $options
     *
     * @return mixed
     */
    protected function convertToModel(array $data, $options = [])
    {
        return $this->getModelService()->populateObject($this->createModelInstance($options), $data, $options);
    }
    /**
     * @param mixed $doc
     * @param array $options
     *
     * @throws \Exception
     */
    protected function restrictModel($doc, array $options = [])
    {
        $this->getModelService()->restrict($doc, $options);
    }
    /**
     * Convert provided data (mixed) to a model property.
     *
     * @param array  $data
     * @param string $propertyName
     * @param array  $options
     *
     * @return mixed
     */
    protected function convertToModelProperty($data, $propertyName, $options = [])
    {
        return $this->getModelService()->populateObjectProperty($this->createModelInstance($options), $data, $propertyName, $options);
    }
    /**
     * @return string
     */
    protected function getModelName()
    {
        return join('.', $this->getTypes());
    }
    /**
     * Prepare fields values
     *
     * @param array $fields
     *
     * @return array
     */
    protected function prepareFields(array $fields = [])
    {
        return $this->getModelService()->prepareFields($this->getModelClass(), $fields);
    }
}
