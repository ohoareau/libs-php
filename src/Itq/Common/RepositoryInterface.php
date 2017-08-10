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

use Iterator;
use Traversable;

/**
 * @author itiQiti Dev Team <opensource@itiqiti.com>
 */
interface RepositoryInterface
{
    /**
     * Create a new document based on specified data.
     *
     * @param array $data
     * @param array $options
     *
     * @return array
     */
    public function create($data, $options = []);
    /**
     * Create multiple new documents based on specified bulk data.
     *
     * @param array $bulkData
     * @param array $options
     *
     * @return array
     */
    public function createBulk($bulkData, $options = []);
    /**
     * Retrieve specified document by id.
     *
     * @param string $id
     * @param array  $fields
     * @param array  $options
     *
     * @return array
     *
     * @throws \Exception
     */
    public function get($id, $fields = [], $options = []);
    /**
     * Retrieve specified document by specified field.
     *
     * @param string $fieldName
     * @param mixed  $fieldValue
     * @param array  $fields
     * @param array  $options
     *
     * @return array
     *
     * @throws \Exception
     */
    public function getBy($fieldName, $fieldValue, $fields = [], $options = []);
    /**
     * Retrieve random document.
     *
     * @param array $fields
     * @param array $criteria
     * @param array $options
     *
     * @return array
     *
     * @throws \Exception
     */
    public function getRandom($fields = [], $criteria = [], $options = []);
    /**
     * Test if specified document exist.
     *
     * @param string $id
     * @param array  $options
     *
     * @return bool
     */
    public function has($id, $options = []);
    /**
     * Test if specified document exist by specified field.
     *
     * @param string $fieldName
     * @param mixed  $fieldValue
     * @param array  $options
     *
     * @return bool
     */
    public function hasBy($fieldName, $fieldValue, $options = []);
    /**
     * Test if specified document not exist.
     *
     * @param string $id
     * @param array  $options
     *
     * @return bool
     */
    public function hasNot($id, $options = []);
    /**
     * Check if specified document exist.
     *
     * @param string $id
     * @param array  $options
     *
     * @return $this
     */
    public function checkExist($id, $options = []);
    /**
     * @param string $field
     * @param mixed  $value
     * @param array  $options
     *
     * @return $this
     *
     * @throws \Exception
     */
    public function checkExistBy($field, $value, $options = []);
    /**
     * @param string $field
     * @param array  $values
     * @param array  $options
     *
     * @return $this
     *
     * @throws \Exception
     */
    public function checkExistByBulk($field, array $values, $options = []);
    /**
     * Check if specified document not exist.
     *
     * @param string $id
     * @param array  $options
     *
     * @return $this
     */
    public function checkNotExist($id, $options = []);
    /**
     * @param string $field
     * @param array  $values
     * @param array  $options
     *
     * @return $this
     *
     * @throws \Exception
     */
    public function checkNotExistByBulk($field, array $values, $options = []);
    /**
     * @param string $field
     * @param mixed  $value
     * @param array  $options
     *
     * @return $this
     *
     * @throws \Exception
     */
    public function checkNotExistBy($field, $value, $options = []);
    /**
     * Count documents matching specified criteria.
     *
     * @param array $criteria
     * @param array $options
     *
     * @return int
     */
    public function count($criteria = [], $options = []);
    /**
     * Retrieve the documents matching the specified criteria, and optionally filter page.
     *
     * @param array    $criteria
     * @param array    $fields
     * @param int|null $limit
     * @param int      $offset
     * @param array    $sorts
     * @param array    $options
     *
     * @return Iterator|Traversable
     */
    public function find($criteria = [], $fields = [], $limit = null, $offset = 0, $sorts = [], $options = []);
    /**
     * Delete the specified document.
     *
     * @param string $id
     * @param array  $options
     *
     * @return array
     */
    public function delete($id, $options = []);
    /**
     * Delete multiple document specified with their id.
     *
     * @param array $bulkIds
     * @param array $options
     *
     * @return array
     */
    public function deleteBulk($bulkIds, $options = []);
    /**
     * Delete documents matching specified criteria.
     *
     * @param array $criteria
     * @param array $options
     *
     * @return array
     */
    public function deleteFound($criteria, $options = []);
    /**
     * Set the specified property of the specified document.
     *
     * @param string $id
     * @param string $property
     * @param mixed  $value
     * @param array  $options
     *
     * @return $this
     */
    public function setProperty($id, $property, $value, $options = []);
    /**
     * Set the specified properties of the specified document.
     *
     * @param string $id
     * @param array  $values
     * @param array  $options
     *
     * @return $this
     */
    public function setProperties($id, $values, $options = []);
    /**
     * Increment specified property of the specified document.
     *
     * @param string $id
     * @param string $property
     * @param mixed  $value
     * @param array  $options
     *
     * @return $this
     */
    public function incrementProperty($id, $property, $value = 1, $options = []);
    /**
     * Increment specified properties of the specified document.
     *
     * @param string $id
     * @param array  $values
     * @param array  $options
     *
     * @return $this
     */
    public function incrementProperties($id, $values, $options = []);
    /**
     * Decrement specified property of the specified document.
     *
     * @param string $id
     * @param string $property
     * @param mixed  $value
     * @param array  $options
     *
     * @return $this
     */
    public function decrementProperty($id, $property, $value = 1, $options = []);
    /**
     * Decrement specified properties of the specified document.
     *
     * @param string $id
     * @param array  $values
     * @param array  $options
     *
     * @return $this
     */
    public function decrementProperties($id, $values, $options = []);
    /**
     * Unset the specified property of the specified document.
     *
     * @param string       $id
     * @param string|array $property
     * @param array        $options
     *
     * @return $this
     */
    public function unsetProperty($id, $property, $options = []);
    /**
     * Alter (raw update) the specified document with the specified data.
     *
     * @param string|array $id      primary key or criteria array
     * @param array        $data
     * @param array        $options
     *
     * @return $this
     */
    public function alter($id, $data, $options = []);
    /**
     * Update the specified document with the specified data.
     *
     * @param string $id
     * @param array  $data
     * @param array  $options
     *
     * @return $this
     */
    public function update($id, $data, $options = []);
    /**
     * Update multiple document specified with their data.
     *
     * @param array $bulkData
     * @param array $options
     *
     * @return array
     */
    public function updateBulk($bulkData, $options = []);
    /**
     * Reset the specified list property of the specified document.
     *
     * @param string|array $id
     * @param string       $property
     * @param array        $options
     *
     * @return $this
     */
    public function resetListProperty($id, $property, $options = []);
    /**
     * Set the specified hash property of the specified document.
     *
     * @param string|array $id
     * @param string       $property
     * @param array        $data
     * @param array        $options
     *
     * @return $this
     */
    public function setHashProperty($id, $property, array $data, $options = []);
    /**
     * Return the specified property of the specified document.
     *
     * @param string $id
     * @param string $property
     * @param array  $options
     *
     * @return mixed
     *
     * @throws \Exception
     */
    public function getProperty($id, $property, $options = []);
    /**
     * Return the specified property of the specified document.
     *
     * @param string $id
     * @param string $property
     * @param mixed  $defaultValue
     * @param array  $options
     *
     * @return mixed
     *
     * @throws \Exception
     */
    public function getPropertyIfExist($id, $property, $defaultValue = null, $options = []);
    /**
     * Return the specified property as a list of the specified document.
     *
     * @param string|array $id
     * @param string       $property
     * @param array        $options
     *
     * @return mixed
     *
     * @throws \Exception
     */
    public function getListProperty($id, $property, $options = []);
    /**
     * Return the specified property as a hash of the specified document.
     *
     * @param string|array $id
     * @param string       $property
     * @param array        $fields
     * @param array        $options
     *
     * @return mixed
     *
     * @throws \Exception
     */
    public function getHashProperty($id, $property, $fields = [], $options = []);
    /**
     * Test if specified property is present in specified document.
     *
     * @param string $id
     * @param string $property
     * @param array  $options
     *
     * @return bool
     */
    public function hasProperty($id, $property, $options = []);
    /**
     * Check if specified property is present in specified document.
     *
     * @param string $id
     * @param string $property
     * @param array  $options
     *
     * @return $this
     */
    public function checkPropertyExist($id, $property, $options = []);
    /**
     * Check if specified property is not present in specified document.
     *
     * @param string|array $id
     * @param string       $property
     * @param array        $options
     *
     * @return $this
     *
     * @throws \Exception
     */
    public function checkPropertyNotExist($id, $property, $options = []);
    /**
     * Create the specified index.
     *
     * @param array $index
     * @param array $options
     *
     * @return $this
     */
    public function createIndex($index, $options = []);
    /**
     * Create the specified indexes.
     *
     * @param array $indexes
     * @param array $options
     *
     * @return $this
     */
    public function createIndexes($indexes, $options = []);
}
