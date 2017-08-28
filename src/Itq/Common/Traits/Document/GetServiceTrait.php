<?php

/*
 * This file is part of the COMMON package.
 *
 * (c) itiQiti SAS <opensource@itiqiti.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Itq\Common\Traits\Document;

use Itq\Common\RepositoryInterface;

/**
 * @author itiQiti Dev Team <opensource@itiqiti.com>
 */
trait GetServiceTrait
{
    /**
     * @param string $id
     * @param string $property
     * @param array  $fields
     * @param array  $extraCriteria
     * @param array  $options
     *
     * @return object
     *
     * @throws \Exception
     */
    public function getEmbedded($id, $property, array $fields = [], array $extraCriteria = [], array $options = [])
    {
        $propertyFields = [];

        foreach ($fields as $k => $v) {
            if (is_numeric($k)) {
                $k = $v;
            }
            $propertyFields[$property.'.'.$k] = true;
        }

        foreach ($extraCriteria as $k => $v) {
            $propertyFields[$property.'.'.$k] = true;
        }

        $doc = $this->get($id, $propertyFields, $options);

        if (!isset($doc->$property)) {
            throw $this->createNotFoundException('doc.unknown_embedded', $this->getFullType(' '), $id, $property);
        }

        $embedded = $doc->$property;

        if (!is_object($embedded)) {
            throw $this->createMalformedException('doc.malformed_embedded', $this->getFullType(' '), $id, $property);
        }

        $found = true;

        foreach ($extraCriteria as $k => $v) {
            if (!property_exists($embedded, $k) || $embedded->$k !== $v) {
                $found = false;
                break;
            }
        }

        if (!$found) {
            throw $this->createMalformedException('doc.unknown_embedded', $this->getFullType(' '), $id, $property);
        }

        return $embedded;
    }
    /**
     * Return the property of the specified document.
     *
     * @param mixed  $id
     * @param string $property
     * @param array  $options
     *
     * @return mixed
     */
    public function getProperty($id, $property, $options = [])
    {
        return $this->convertToModelProperty($this->getRepository()->getProperty($id, $property, $options), $property, ['operation' => 'retrieve']);
    }
    /**
     * Return the property of the specified document.
     *
     * @param string $fieldName
     * @param mixed  $fieldValue
     * @param string $property
     * @param array  $options
     *
     * @return mixed
     */
    public function getPropertyBy($fieldName, $fieldValue, $property, $options = [])
    {
        return $this->convertToModelProperty($this->getRepository()->getProperty([$fieldName => $fieldValue], $property, $options), $property, ['operation' => 'retrieve']);
    }
    /**
     * Return the property of the specified document if exist or default value otherwise.
     *
     * @param mixed  $id
     * @param string $property
     * @param mixed  $defaultValue
     * @param array  $options
     *
     * @return mixed
     */
    public function getPropertyIfExist($id, $property, $defaultValue = null, $options = [])
    {
        return $this->getRepository()->getPropertyIfExist($id, $property, $defaultValue, $options);
    }
    /**
     * Test if specified document exist.
     *
     * @param mixed $id
     * @param array $options
     *
     * @return bool
     */
    public function has($id, $options = [])
    {
        return $this->getRepository()->has($id, $options);
    }
    /**
     * Test if specified document exist by specified field.
     *
     * @param string $fieldName
     * @param mixed  $fieldValue
     * @param array  $options
     *
     * @return bool
     */
    public function hasBy($fieldName, $fieldValue, $options = [])
    {
        return $this->getRepository()->hasBy($fieldName, $fieldValue, $options);
    }
    /**
     * Test if specified document does not exist.
     *
     * @param mixed $id
     * @param array $options
     *
     * @return bool
     */
    public function hasNot($id, $options = [])
    {
        return $this->getRepository()->hasNot($id, $options);
    }
    /**
     * Return the specified document.
     *
     * @param mixed $id
     * @param array $fields
     * @param array $options
     *
     * @return mixed
     */
    public function get($id, $fields = [], $options = [])
    {
        $fetchedFields = $this->prepareFields($fields);

        return $this->convertToModel($this->getRepository()->get($id, $fetchedFields, $options), ['docId' => $id, 'requestedFields' => $fields, 'fetchedFields' => $fetchedFields, 'operation' => 'retrieve'] + $options);
    }
    /**
     * Return the list of the specified documents.
     *
     * @param array $ids
     * @param array $fields
     * @param array $options
     *
     * @return mixed
     */
    public function getBulk($ids, $fields = [], $options = [])
    {
        $docs          = [];
        $fetchedFields = $this->prepareFields($fields);

        foreach ($this->getRepository()->find(['_id' => $ids], $fetchedFields, null, 0, [], $options) as $k => $v) {
            $docs[$k] = $this->convertToModel($v, ['docId' => $k, 'requestedFields' => $fields, 'fetchedFields' => $fetchedFields, 'operation' => 'retrieve'] + $options);
        }

        return $docs;
    }
    /**
     * Return the specified document by the specified field.
     *
     * @param string $fieldName
     * @param mixed  $fieldValue
     * @param array  $fields
     * @param array  $options
     *
     * @return mixed
     */
    public function getBy($fieldName, $fieldValue, $fields = [], $options = [])
    {
        $fetchedFields = $this->prepareFields($fields);

        return $this->convertToModel($this->getRepository()->getBy($fieldName, $fieldValue, $fetchedFields, $options), ['doc'.ucfirst($fieldName) => $fieldValue, 'requestedFields' => $fields, 'fetchedFields' => $fetchedFields, 'operation' => 'retrieve'] + $options);
    }
    /**
     * Return a random document matching the specified criteria.
     *
     * @param array $fields
     * @param array $criteria
     * @param array $options
     *
     * @return mixed
     */
    public function getRandom($fields = [], $criteria = [], $options = [])
    {
        $fetchedFields = $this->prepareFields($fields);

        return $this->convertToModel($this->getRepository()->getRandom($fetchedFields, $criteria, $options), ['requestedFields' => $fields, 'fetchedFields' => $fetchedFields, 'operation' => 'retrieve'] + $options);
    }
    /**
     * Check if specified document exist.
     *
     * @param mixed $id
     * @param array $options
     *
     * @return $this
     *
     * @throws \Exception
     */
    public function checkExist($id, $options = [])
    {
        $this->getRepository()->checkExist($id, $options);

        return $this;
    }
    /**
     * Check if specified document exist by specified field and value.
     *
     * @param string $field
     * @param mixed  $value
     * @param array  $options
     *
     * @return $this
     *
     * @throws \Exception
     */
    public function checkExistBy($field, $value, $options = [])
    {
        $this->getRepository()->checkExistBy($field, $value, $options);

        return $this;
    }
    /**
     * Check if specified document not exist by specified field and value.
     *
     * @param string $field
     * @param mixed  $value
     * @param array  $options
     *
     * @return $this
     *
     * @throws \Exception
     */
    public function checkNotExistBy($field, $value, $options = [])
    {
        $this->getRepository()->checkNotExistBy($field, $value, $options);

        return $this;
    }
    /**
     * Check if specified document exist by specified field and values.
     *
     * @param string $field
     * @param array  $values
     * @param array  $options
     *
     * @return $this
     *
     * @throws \Exception
     */
    public function checkExistByBulk($field, array $values, $options = [])
    {
        $this->getRepository()->checkExistByBulk($field, $values, $options);

        return $this;
    }
    /**
     * Check is specified document does not exist.
     *
     * @param mixed $id
     * @param array $options
     *
     * @return $this
     *
     * @throws \Exception
     */
    public function checkNotExist($id, $options = [])
    {
        $this->getRepository()->checkNotExist($id, $options);

        return $this;
    }
    /**
     * @return RepositoryInterface
     */
    abstract public function getRepository();
}
