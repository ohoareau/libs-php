<?php

/*
 * This file is part of the COMMON package.
 *
 * (c) itiQiti SAS <opensource@itiqiti.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Itq\Common\Traits\SubDocument;

use Itq\Common\RepositoryInterface;

/**
 * Get service trait.
 *
 * @author itiQiti Dev Team <opensource@itiqiti.com>
 */
trait GetServiceTrait
{
    /**
     * Return the property of the specified document.
     *
     * @param mixed  $parentId
     * @param mixed  $id
     * @param string $property
     * @param array  $options
     *
     * @return mixed
     */
    public function getProperty($parentId, $id, $property, $options = [])
    {
        return $this->convertToModelProperty($this->getRepository()->getProperty($parentId, $this->getRepoKey([$id, $property]), $options), $property, ['operation' => 'retrieve']);
    }
    /**
     * Return the property of the specified document if exist or default value otherwise.
     *
     * @param mixed  $parentId
     * @param mixed  $id
     * @param string $property
     * @param mixed  $defaultValue
     * @param array  $options
     *
     * @return mixed
     */
    public function getPropertyIfExist($parentId, $id, $property, $defaultValue = null, $options = [])
    {
        return $this->getRepository()->getPropertyIfExist($parentId, $this->getRepoKey([$id, $property]), $defaultValue, $options);
    }
    /**
     * Test if specified document exist.
     *
     * @param string $parentId
     * @param mixed  $id
     * @param array  $options
     *
     * @return bool
     */
    public function has($parentId, $id, $options = [])
    {
        return $this->getRepository()->hasProperty($parentId, $this->getRepoKey([$id]), $options);
    }
    /**
     * Test if specified document exist by specified field.
     *
     * @param string $parentId
     * @param string $fieldName
     * @param mixed  $fieldValue
     * @param array  $options
     *
     * @return bool
     */
    public function hasBy($parentId, $fieldName, $fieldValue, $options = [])
    {
        return 0 < count($this->find($parentId, [$fieldName => $fieldValue], ['id'], 1, 0, $options));
    }
    /**
     * Test if specified document does not exist.
     *
     * @param string $parentId
     * @param mixed  $id
     * @param array  $options
     *
     * @return bool
     */
    public function hasNot($parentId, $id, $options = [])
    {
        return !$this->has($parentId, $id, $options);
    }
    /**
     * Return the specified document.
     *
     * @param string $parentId
     * @param mixed  $id
     * @param array  $fields
     * @param array  $options
     *
     * @return mixed
     */
    public function get($parentId, $id, $fields = [], $options = [])
    {
        $fetchedFields = $this->prepareFields($fields);

        return $this->convertToModel(
            $this->getRepository()->getHashProperty($parentId, $this->getRepoKey([$id], $options + ['fieldMapping' => ['parentToken' => 'token']]), $fetchedFields, $options),
            ['requestedFields' => $fields, 'fetchedFields' => $fetchedFields, 'parentId' => $parentId, 'operation' => 'retrieve'] + $options
        );
    }
    /**
     * Check if specified document exist.
     *
     * @param string $parentId
     * @param mixed  $id
     * @param array  $options
     *
     * @return $this
     *
     * @throws \Exception
     */
    public function checkExist($parentId, $id, $options = [])
    {
        $this->getRepository()->checkPropertyExist($parentId, $this->getRepoKey([$id], $options), $options);

        return $this;
    }
    /**
     * Check is specified document does not exist.
     *
     * @param string $parentId
     * @param mixed  $id
     * @param array  $options
     *
     * @return $this
     *
     * @throws \Exception
     */
    public function checkNotExist($parentId, $id, $options = [])
    {
        $this->getRepository()->checkPropertyNotExist($parentId, $this->getRepoKey([$id], $options), $options);

        return $this;
    }
    /**
     * Returns the parent id based on the specified field and value to select it.
     *
     * @param string $field
     * @param mixed  $value
     *
     * @return string
     */
    public function getParentIdBy($field, $value)
    {
        return (string) $this->getRepository()->get([$field => $value], ['_id'])['_id'];
    }
    /**
     * @return RepositoryInterface
     */
    abstract public function getRepository();
}
