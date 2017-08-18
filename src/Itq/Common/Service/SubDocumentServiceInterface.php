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

/**
 * @author itiQiti Dev Team <opensource@itiqiti.com>
 */
interface SubDocumentServiceInterface
{
    /**
     * Return the document types.
     *
     * @return string
     */
    public function getTypes();
    /**
     * @param string $field
     * @param mixed  $value
     *
     * @return string
     */
    public function getParentIdBy($field, $value);
    /**
     * Retrieve the documents matching the specified criteria.
     *
     * @param mixed    $parentId
     * @param array    $criteria
     * @param array    $fields
     * @param null|int $limit
     * @param int      $offset
     * @param array    $sorts
     * @param array    $options
     *
     * @return array
     */
    public function find($parentId, $criteria = [], $fields = [], $limit = null, $offset = 0, $sorts = [], $options = []);
    /**
     * Retrieve the documents matching the specified criteria.
     *
     * @param mixed    $fieldName
     * @param mixed    $fieldValue
     * @param array    $criteria
     * @param array    $fields
     * @param null|int $limit
     * @param int      $offset
     * @param array    $sorts
     * @param array    $options
     *
     * @return array
     */
    public function findBy($fieldName, $fieldValue, $criteria = [], $fields = [], $limit = null, $offset = 0, $sorts = [], $options = []);
    /**
     * Retrieve the documents matching the specified criteria and return a page with total count.
     *
     * @param mixed    $parentId
     * @param array    $criteria
     * @param array    $fields
     * @param null|int $limit
     * @param int      $offset
     * @param array    $sorts
     * @param array    $options
     *
     * @return mixed
     */
    public function findWithTotal($parentId, $criteria = [], $fields = [], $limit = null, $offset = 0, $sorts = [], $options = []);
    /**
     * Return the specified document.
     *
     * @param mixed $parentId
     * @param mixed $id
     * @param array $fields
     * @param array $options
     *
     * @return mixed
     */
    public function get($parentId, $id, $fields = [], $options = []);
    /**
     * Create a new document.
     *
     * @param mixed $parentId
     * @param mixed $data
     * @param array $options
     *
     * @return mixed
     */
    public function create($parentId, $data, $options = []);
    /**
     * Create a new document by selecting parent from a specific field.
     *
     * @param string $parentFieldName
     * @param mixed  $parentFieldValue
     * @param mixed  $data
     * @param array  $options
     *
     * @return mixed
     */
    public function createBy($parentFieldName, $parentFieldValue, $data, $options = []);
    /**
     * Create document if not exist or update it.
     *
     * @param mixed $parentId
     * @param mixed $data
     * @param array $options
     *
     * @return mixed
     */
    public function createOrUpdate($parentId, $data, $options = []);
    /**
     * Create a list of documents.
     *
     * @param mixed $parentId
     * @param mixed $bulkData
     * @param array $options
     *
     * @return mixed
     */
    public function createBulk($parentId, $bulkData, $options = []);
    /**
     * Create documents if not exist or update them.
     *
     * @param mixed $parentId
     * @param mixed $bulkData
     * @param array $options
     *
     * @return mixed
     */
    public function createOrUpdateBulk($parentId, $bulkData, $options = []);
    /**
     * Create documents if not exist or delete them.
     *
     * @param mixed $parentId
     * @param mixed $bulkData
     * @param array $options
     *
     * @return mixed
     */
    public function createOrDeleteBulk($parentId, $bulkData, $options = []);
    /**
     * Count documents matching the specified criteria.
     *
     * @param mixed $parentId
     * @param mixed $criteria
     *
     * @return mixed
     */
    public function count($parentId, $criteria = []);
    /**
     * Update the specified document.
     *
     * @param mixed $parentId
     * @param mixed $id
     * @param mixed $data
     * @param array $options
     *
     * @return mixed
     */
    public function update($parentId, $id, $data, $options = []);
    /**
     * Delete the specified document.
     *
     * @param mixed $parentId
     * @param mixed $id
     * @param array $options
     *
     * @return mixed
     */
    public function delete($parentId, $id, $options = []);
    /**
     * Delete the specified documents.
     *
     * @param mixed $parentId
     * @param array $ids
     * @param array $options
     *
     * @return mixed
     */
    public function deleteBulk($parentId, $ids, $options = []);
    /**
     * Purge all the documents matching the specified criteria.
     *
     * @param mixed $parentId
     * @param array $criteria
     * @param array $options
     *
     * @return mixed
     */
    public function purge($parentId, $criteria = [], $options = []);
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
    public function getProperty($parentId, $id, $property, $options = []);
    /**
     * Test if specified document exist.
     *
     * @param mixed $parentId
     * @param mixed $id
     *
     * @return bool
     */
    public function has($parentId, $id);
    /**
     * Test if specified document does not exist.
     *
     * @param mixed $parentId
     * @param mixed $id
     *
     * @return bool
     */
    public function hasNot($parentId, $id);
    /**
     * Check if specified document exist.
     *
     * @param mixed $parentId
     * @param mixed $id
     *
     * @return $this
     *
     * @throws \Exception
     */
    public function checkExist($parentId, $id);
    /**
     * Replace all the specified documents.
     *
     * @param mixed $parentId
     * @param array $bulkData
     * @param array $options
     *
     * @return mixed
     */
    public function replaceBulk($parentId, $bulkData, $options = []);
    /**
     * Check is specified document does not exist.
     *
     * @param mixed $parentId
     * @param mixed $id
     *
     * @return $this
     *
     * @throws \Exception
     */
    public function checkNotExist($parentId, $id);
    /**
     * Increment the specified property of the specified document.
     *
     * @param mixed        $parentId
     * @param mixed        $id
     * @param string|array $property
     * @param int          $value
     *
     * @return $this
     */
    public function increment($parentId, $id, $property, $value = 1);
    /**
     * Decrement the specified property of the specified document.
     *
     * @param mixed        $parentId
     * @param mixed        $id
     * @param string|array $property
     * @param int          $value
     *
     * @return $this
     */
    public function decrement($parentId, $id, $property, $value = 1);
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
    public function hasBy($parentId, $fieldName, $fieldValue, $options = []);
}
