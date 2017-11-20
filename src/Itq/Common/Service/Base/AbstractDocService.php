<?php

/*
 * This file is part of the COMMON package.
 *
 * (c) itiQiti SAS <opensource@itiqiti.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Itq\Common\Service\Base;

use Exception;
use Itq\Common\Traits;

/**
 * Abstract Doc Service.
 *
 * @author itiQiti Dev Team <opensource@itiqiti.com>
 */
abstract class AbstractDocService
{
    use Traits\ServiceTrait;
    use Traits\LoggerAwareTrait;
    use Traits\RepositoryAwareTrait;
    use Traits\ServiceAware\FormServiceAwareTrait;
    use Traits\ServiceAware\ModelServiceAwareTrait;
    use Traits\ServiceAware\MetaDataServiceAwareTrait;
    use Traits\ServiceAware\WorkflowServiceAwareTrait;
    use Traits\ServiceAware\BusinessRuleServiceAwareTrait;
    /**
     * @return int|null
     */
    abstract public function getExpectedTypeCount();
    /**
     * @param array $types
     *
     * @return $this
     *
     * @throws Exception
     */
    public function setTypes(array $types)
    {
        $expectedTypeCount = $this->getExpectedTypeCount();

        if (null !== $expectedTypeCount && count($types) !== $expectedTypeCount) {
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
     * @param array $ids
     * @param array $options
     *
     * @return string
     */
    public function getRepoKey(array $ids = [], $options = [])
    {
        $options += ['pattern' => '%ss', 'skip' => 0, 'separator' => '.'];

        $key    = '';
        $types  = $this->getTypes();
        $toSkip = $options['skip'];
        $sep    = $options['separator'];

        array_shift($types);

        while (count($types)) {
            $type = array_shift($types);
            if (!$toSkip) {
                $key .= ($key ? $sep : '').sprintf($options['pattern'], $type);
            } else {
                $toSkip--;
                if ($toSkip) {
                    continue;
                }
            }

            if (!count($ids)) {
                if (!count($types)) {
                    break;
                }
                $id = 'unknown';
            } else {
                $id = array_shift($ids);
            }

            $this->checkRepoKeyTokenIsValid($id, $sep);

            $key .= ($key ? $sep : '').$id;
        }

        if (count($ids)) {
            foreach ($ids as $id) {
                $this->checkRepoKeyTokenIsValid($id, $sep);
                $key .= ($key ? $sep : '').$id;
            }
        }

        return $key;
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
     * @throws Exception
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
     * @param array  $data
     * @param string $mode
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
        return $this->getModelService()->populateObject($this->createModelInstance($options), $data, $options + ['originalModel' => $this->getModelClass()]);
    }
    /**
     * @param mixed $doc
     * @param array $options
     *
     * @throws Exception
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
    /**
     * @param array $array
     * @param array $ids
     * @param array $options
     *
     * @return array
     */
    protected function mutateArrayToRepoChanges($array, array $ids = [], $options = [])
    {
        $changes = [];

        foreach ($array as $k => $v) {
            $changes[$this->mutateKeyToRepoChangesKey($k, $ids)] = $v;
        }

        unset($options);

        return $changes;
    }

    /**
     * @param string $key
     * @param array  $ids
     * @param array  $options
     *
     * @return string
     */
    protected function mutateKeyToRepoChangesKey($key, array $ids = [], array $options = [])
    {
        unset($options);

        if ($this->isEmptyString($key)) {
            return $this->getRepoKey($ids);
        }

        return sprintf('%s.%s', $this->getRepoKey($ids), $key);
    }
    /**
     * @param string $token
     * @param string $sep
     *
     * @return $this
     *
     * @throws Exception
     */
    protected function checkRepoKeyTokenIsValid($token, $sep)
    {
        if (false !== strpos($token, $sep)) {
            throw $this->createMalformedException("Key token '%s' is invalid (found: %s)", $token, $sep);
        }

        if ($this->isEmptyString($token)) {
            throw $this->createMalformedException('Key token is empty', $token, $sep);
        }

        return $this;
    }
    /**
     * Cast criteria values and restore value keys
     *
     * @param array $criteria
     *
     * @return array
     */
    protected function prepareCriteria(array $criteria = [])
    {
        foreach ($criteria as $criteriaKey => &$criteriaValue) {
            if ('$or' === $criteriaKey) {
                $criteria[$criteriaKey] = $this->prepareCriteria($criteriaValue);
                continue;
            }
            if (false !== strpos($criteriaKey, ':')) {
                unset($criteria[$criteriaKey]);
                $this->prepareCompositeCriteria($criteriaKey, $criteriaValue);
                $criteria[$criteriaKey] = $criteriaValue;
            }
        }

        return $criteria;
    }
    /**
     * Transform composite criteria to normal criteria
     *
     * A composite criteria use this pattern 'key:type' => 'value'
     *
     * @param mixed $key
     * @param mixed $value
     */
    protected function prepareCompositeCriteria(&$key, &$value)
    {
        list($key, $criteriaValueType) = explode(':', $key, 2);

        switch (trim($criteriaValueType)) {
            case 'int':
                $value = (int) $value;
                break;
            case 'string':
                $value = (string) $value;
                break;
            case 'bool':
                $value = (bool) $value;
                break;
            case 'array':
                $value = json_decode($value, true);
                break;
            case 'float':
                $value = (float) $value;
                break;
            default:
                break;
        }
    }
}
