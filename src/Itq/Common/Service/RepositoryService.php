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
use MongoDuplicateKeyException;
use Itq\Common\RepositoryInterface;

/**
 * @author itiQiti Dev Team <opensource@itiqiti.com>
 */
class RepositoryService implements RepositoryInterface
{
    use Traits\ServiceTrait;
    use Traits\LoggerAwareTrait;
    use Traits\TranslatorAwareTrait;
    use Traits\ServiceAware\DatabaseServiceAwareTrait;
    /**
     * Set the connection options.
     *
     * @param array $options
     *
     * @return $this
     */
    public function setConnectionOptions($options)
    {
        return $this->setParameter('connectionOptions', $options);
    }
    /**
     * Return the connection options.
     *
     * @return array
     */
    public function getConnectionOptions()
    {
        return $this->getParameter('connectionOptions', []);
    }
    /**
     * Set the underlying collection name.
     *
     * @param string $collectionName
     *
     * @return $this
     */
    public function setCollectionName($collectionName)
    {
        return $this->setParameter('collectionName', $collectionName);
    }
    /**
     * Return the underlying collection name.
     *
     * @return string
     */
    public function getCollectionName()
    {
        return $this->getParameter('collectionName');
    }
    /**
     * Create a new document based on specified data.
     *
     * @param array $data
     * @param array $options
     *
     * @return array
     *
     * @throws Exception
     */
    public function create($data, $options = [])
    {
        try {
            return $this->getDatabaseService()
                ->insert(
                    $this->getCollectionName(),
                    $data,
                    $options + ['new' => true] + $this->getConnectionOptions()
                )
            ;
        } catch (MongoDuplicateKeyException $e) {
            throw $this->createDuplicatedException('doc.exist_basic/'.$this->getCollectionName(), ucfirst($this->getCollectionName()));
        }
    }
    /**
     * Create multiple new documents based on specified bulk data.
     *
     * @param array $bulkData
     * @param array $options
     *
     * @return array
     *
     * @throws Exception
     */
    public function createBulk($bulkData, $options = [])
    {
        try {
            return $this->getDatabaseService()->bulkInsert(
                $this->getCollectionName(),
                $bulkData,
                $options + ['new' => true] + $this->getConnectionOptions()
            );
        } catch (MongoDuplicateKeyException $e) {
            throw $this->createDuplicatedException('doc.exist_basic/'.$this->getCollectionName(), ucfirst($this->getCollectionName()));
        }
    }
    /**
     * Retrieve specified document by id.
     *
     * @param string|array $id
     * @param array        $fields
     * @param array        $options
     *
     * @return array
     *
     * @throws Exception
     */
    public function get($id, $fields = [], $options = [])
    {
        $options += ['fieldMapping' => []];

        if (!is_array($options['fieldMapping'])) {
            $options['fieldMapping'] = [];
        }

        $collectionName = $this->getCollectionName();

        if (!is_array($id)) {
            $id = ['_id' => $id];
        }

        $doc = $this->getDatabaseService()->findOne(
            $collectionName,
            $id,
            array_merge($fields, array_values($options['fieldMapping'])),
            $options + $this->getConnectionOptions()
        );

        if (null === $doc) {
            throw $this->createDocUnknownException($collectionName, $id, $options);
        }

        foreach ($options['fieldMapping'] as $k => $v) {
            $vv = isset($doc[$v]) ? $doc[$v] : null;
            unset($doc[$v]);
            $doc[$k] = $vv;
        }

        return $doc;
    }
    /**
     * @param string $collectionName
     * @param mixed  $id
     * @param array  $options
     *
     * @return Exception
     */
    public function createDocUnknownException($collectionName, $id, $options = [])
    {
        unset($options);

        switch (count($id)) {
            case 0:
                return $this->createNotFoundException('doc.unknown_basic', $collectionName);
            case 1:
                $kk = array_keys($id)[0];
                if ('_id' === $kk) {
                    return $this->createNotFoundException('doc.unknown/'.$collectionName, $collectionName, array_values($id)[0]);
                }

                return $this->createNotFoundException('doc.unknown_by_property/'.$collectionName, $collectionName, $kk, array_values($id)[0]);
            default:
                return $this->createNotFoundException('doc.unknown_by/'.$collectionName, $collectionName, json_encode($id));
        }
    }
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
     * @throws Exception
     */
    public function getBy($fieldName, $fieldValue, $fields = [], $options = [])
    {
        return $this->get([$fieldName => $fieldValue], $fields, $options + $this->getConnectionOptions());
    }
    /**
     * Retrieve random document.
     *
     * @param array $fields
     * @param array $criteria
     * @param array $options
     *
     * @return array
     *
     * @throws Exception
     */
    public function getRandom($fields = [], $criteria = [], $options = [])
    {
        srand(microtime(true));

        $docs     = $this->find($criteria, ['_id']);
        $index    = rand(0, method_exists($docs, 'count') ? ($docs->count() - 1) : 0);
        $current  = 0;
        $document = null;

        foreach ($docs as $doc) {
            if ($current === $index) {
                $document = $doc;
                break;
            }
            $current++;
        }

        return $this->get($document['_id'], $fields, $criteria);
    }
    /**
     * Test if specified document exist.
     *
     * @param string|array $id
     * @param array        $options
     *
     * @return bool
     */
    public function has($id, $options = [])
    {
        if (!is_array($id)) {
            $id = ['_id' => $id];
        }

        return null !== $this->getDatabaseService()->findOne(
            $this->getCollectionName(),
            $id,
            [],
            $options + $this->getConnectionOptions()
        );
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
        return $this->has([$fieldName => $fieldValue], $options);
    }
    /**
     * Test if specified document not exist.
     *
     * @param string|array $id
     * @param array        $options
     *
     * @return bool
     */
    public function hasNot($id, $options = [])
    {
        return !$this->has($id, $options);
    }
    /**
     * Check if specified document exist.
     *
     * @param string|array $id
     * @param array        $options
     *
     * @return $this
     *
     * @throws Exception
     */
    public function checkExist($id, $options = [])
    {
        if (!$this->has($id)) {
            throw $this->createDocUnknownException($this->getCollectionName(), $id, $options);
        }

        return $this;
    }
    /**
     * @param string $field
     * @param mixed  $value
     * @param array  $options
     *
     * @return $this
     *
     * @throws Exception
     */
    public function checkExistBy($field, $value, $options = [])
    {
        return $this->checkExistByBulk($field, [$value], $options);
    }
    /**
     * @param string $field
     * @param array  $values
     * @param array  $options
     *
     * @return $this
     *
     * @throws Exception
     */
    public function checkExistByBulk($field, array $values, $options = [])
    {
        if ('id' === $field) {
            $field = '_id';
        }

        $docs = $this->find([$field => ['$in' => $values]], ['_id', $field], null, 0, [], $options);

        $found = [];

        foreach ($docs as $doc) {
            $found[(string) $doc[$field]] = true;
        }

        $notFound = array_diff($values, array_keys($found));

        if (0 < count($notFound)) {
            throw $this->createNotFoundException('doc.unknown_multiple/'.$this->getCollectionName(), $this->getCollectionName(), join(', ', $notFound));
        }

        return $this;
    }
    /**
     * @param string $field
     * @param array  $values
     * @param array  $options
     *
     * @return $this
     *
     * @throws Exception
     */
    public function checkNotExistByBulk($field, array $values, $options = [])
    {
        if ('id' === $field) {
            $field = '_id';
        }

        if (0 < $this->count([$field => ['$in' => $values]], $options)) {
            throw $this->createDuplicatedException('doc.exist_multiple/'.$this->getCollectionName(), $this->getCollectionName(), $field, join(', ', $values));
        }

        return $this;
    }
    /**
     * @param string $field
     * @param mixed  $value
     * @param array  $options
     *
     * @return $this
     *
     * @throws Exception
     */
    public function checkNotExistBy($field, $value, $options = [])
    {
        return $this->checkNotExistByBulk($field, [$value], $options);
    }
    /**
     * Check if specified document not exist.
     *
     * @param string|array $id
     * @param array        $options
     *
     * @return $this
     *
     * @throws Exception
     */
    public function checkNotExist($id, $options = [])
    {
        if ($this->has($id, $options)) {
            $collectionName = $this->getCollectionName();
            switch (count($id)) {
                case 0:
                    throw $this->createNotFoundException('doc.exist_basic/'.$collectionName, $collectionName);
                case 1:
                    throw $this->createNotFoundException('doc.exist_by_property/'.$collectionName, $collectionName, array_keys($id)[0], array_values($id)[0]);
                default:
                    throw $this->createNotFoundException('doc.exist_by/'.$collectionName, $collectionName, json_encode($id));
            }
        }

        return $this;
    }
    /**
     * Count documents matching specified criteria.
     *
     * @param array $criteria
     * @param array $options
     *
     * @return int
     */
    public function count($criteria = [], $options = [])
    {
        return $this->getDatabaseService()
            ->count($this->getCollectionName(), $criteria, $options + $this->getConnectionOptions());
    }
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
     * @return \Traversable
     */
    public function find($criteria = [], $fields = [], $limit = null, $offset = 0, $sorts = [], $options = [])
    {
        return $this->getDatabaseService()->find(
            $this->getCollectionName(),
            $criteria,
            $fields,
            $limit,
            $offset,
            $sorts,
            $options + $this->getConnectionOptions()
        );
    }
    /**
     * Delete the specified document.
     *
     * @param string|array $id
     * @param array        $options
     *
     * @return mixed
     */
    public function delete($id, $options = [])
    {
        if (!is_array($id)) {
            $id = ['_id' => $id];
        }

        $this->getDatabaseService()->remove(
            $this->getCollectionName(),
            $id,
            $options + ['justOne' => true] + $this->getConnectionOptions()
        );

        return $this;
    }
    /**
     * Delete documents matching specified criteria.
     *
     * @param array $criteria
     * @param array $options
     *
     * @return array
     */
    public function deleteFound($criteria, $options = [])
    {
        return $this->delete($criteria, $options + ['justOne' => false] + $this->getConnectionOptions());
    }
    /**
     * Set the specified property of the specified document.
     *
     * @param string|array $id
     * @param string       $property
     * @param mixed        $value
     * @param array        $options
     *
     * @return $this
     */
    public function setProperty($id, $property, $value, $options = [])
    {
        return $this->alter($id, ['$set' => [$property => $value]], $options);
    }
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
    public function setHashProperty($id, $property, array $data, $options = [])
    {
        return $this->setProperty($id, $property, (object) $data, $options);
    }
    /**
     * Reset the specified list property of the specified document.
     *
     * @param string|array $id
     * @param string       $property
     * @param array        $options
     *
     * @return $this
     */
    public function resetListProperty($id, $property, $options = [])
    {
        return $this->setProperty($id, $property, (object) [], $options);
    }
    /**
     * Set the specified properties of the specified document.
     *
     * @param string|array $id
     * @param array        $values
     * @param array        $options
     *
     * @return $this
     */
    public function setProperties($id, $values, $options = [])
    {
        if (!count($values)) {
            return $this;
        }

        return $this->alter($id, ['$set' => $values]);
    }
    /**
     * Increment specified property of the specified document.
     *
     * @param string|array $id
     * @param string       $property
     * @param mixed        $value
     * @param array        $options
     *
     * @return $this
     */
    public function incrementProperty($id, $property, $value = 1, $options = [])
    {
        return $this->alter($id, ['$inc' => [$property => $value]], $options);
    }
    /**
     * Decrement specified property of the specified document.
     *
     * @param string|array $id
     * @param string       $property
     * @param mixed        $value
     * @param array        $options
     *
     * @return $this
     */
    public function decrementProperty($id, $property, $value = 1, $options = [])
    {
        return $this->alter($id, ['$inc' => [$property => - $value]], $options);
    }
    /**
     * Increment specified properties of the specified document.
     *
     * @param string|array $id
     * @param array        $values
     * @param array        $options
     *
     * @return $this
     */
    public function incrementProperties($id, $values, $options = [])
    {
        if (!count($values)) {
            return $this;
        }

        return $this->alter($id, ['$inc' => $values], $options);
    }
    /**
     * Decrement specified properties of the specified document.
     *
     * @param string|array $id
     * @param array        $values
     * @param array        $options
     *
     * @return $this
     */
    public function decrementProperties($id, $values, $options = [])
    {
        if (!count($values)) {
            return $this;
        }

        return $this->alter($id, ['$inc' => array_map(function ($v) {
            return - $v;
        }, $values), ], $options);
    }
    /**
     * Unset the specified property of the specified document.
     *
     * @param string|array $id
     * @param string|array $property
     * @param array        $options
     *
     * @return $this
     */
    public function unsetProperty($id, $property, $options = [])
    {
        if (!is_array($property)) {
            $property = [$property];
        }

        return $this->alter($id, ['$unset' => array_fill_keys($property, '')], $options);
    }
    /**
     * Update the specified document with the specified data.
     *
     * @param string|array $id
     * @param array        $data
     * @param array        $options
     *
     * @return $this
     *
     * @throws Exception
     */
    public function update($id, $data, $options = [])
    {
        $sets = [];
        $pulls = [];
        $addToSets = [];

        foreach ($data as $k => $v) {
            $lastDoublePointPos = strrpos($k, ':');
            if (false !== $lastDoublePointPos) {
                $modifier = substr($k, $lastDoublePointPos + 1);
                switch ($modifier) {
                    case 'toggle':
                        if (!is_array($v)) {
                            $v = explode(',', $v);
                        }
                        unset($data[$k]);
                        $toggleAdd = [];
                        $toggleRemove = [];
                        foreach (array_values($v) as $vv) {
                            if ('!' === substr($vv, 0, 1)) {
                                $toggleRemove[] = substr($vv, 1);
                            } else {
                                $toggleAdd[] = $vv;
                            }
                        }
                        if (count($toggleRemove)) {
                            $pulls[substr($k, 0, $lastDoublePointPos)] = ['$in' => $toggleRemove];
                        }
                        if (count($toggleAdd)) {
                            $addToSets[substr($k, 0, $lastDoublePointPos)] = ['$each' => $toggleAdd];
                        }
                        break;
                    default:
                        $sets[$k] = $v;
                        break;
                }
            } else {
                $sets[$k] = $v;
            }
        }

        $updates1 = [];
        $updates2 = [];

        if (count($sets)) {
            $updates1['$set'] = $sets;
        }

        if (count($addToSets)) {
            $updates1['$addToSet'] = $addToSets;
        }

        if (count($pulls)) {
            // MongoDB does not allow updating with $addToSet and $pull in the same time
            if (isset($updates1['$addToSet'])) {
                $updates2['$pull'] = $pulls;
            } else {
                $updates1['$pull'] = $pulls;
            }
        }

        $updateCount = 0;
        $r1          = null;
        $r2          = null;

        if (count($updates1)) {
            $r1 = $this->alter($id, $updates1, ['upsert' => false] + $options);
            $updateCount++;
        }

        if (count($updates2)) {
            $r2 = $this->alter($id, $updates2, ['upsert' => false] + $options);
            $updateCount++;
        }

        if ($updateCount <= 0) {
                throw $this->createRequiredException('doc.update.empty/'.$this->getCollectionName());
        }

        return $r1 ? $r1 : ($r2 ? $r2 : $r1);
    }
    /**
     * Alter (raw update) the specified document with the specified data.
     *
     * @param string|array $id      primary key or criteria array
     * @param array        $data
     * @param array        $options
     *
     * @return $this
     */
    public function alter($id, $data, $options = [])
    {
        $criteria = is_array($id) ? $id : ['_id' => $id];

        return $this->getDatabaseService()->update(
            $this->getCollectionName(),
            $criteria,
            $data,
            ['upsert' => false] + $options + $this->getConnectionOptions()
        );
    }
    /**
     * Update multiple document specified with their data.
     *
     * @param array $bulkData
     * @param array $options
     *
     * @return array
     */
    public function updateBulk($bulkData, $options = [])
    {
        $docs = [];

        foreach ($bulkData as $id => $data) {
            $docs[$id] = $this->update($id, $data, $options);
        }

        return $docs;
    }
    /**
     * Delete multiple document specified with their id.
     *
     * @param array $bulkIds
     * @param array $options
     *
     * @return array
     */
    public function deleteBulk($bulkIds, $options = [])
    {
        $properties  = [];

        foreach ($bulkIds as $id) {
            $properties[$id] = ['_id' => $id];
        }

        $this->deleteFound(['$or' => array_values($properties)], $options);

        return array_combine(array_keys($properties), array_keys($properties));
    }
    /**
     * Return the specified property of the specified document.
     *
     * @param string|array $id
     * @param string       $property
     * @param array        $options
     *
     * @return mixed
     *
     * @throws Exception
     */
    public function getProperty($id, $property, $options = [])
    {
        $options += ['fieldMapping' => []];

        if (!is_array($options['fieldMapping'])) {
            $options['fieldMapping'] = [];
        }

        $fields = [];

        if (isset($options['fields']) && is_array($options['fields']) && count($options['fields'])) {
            foreach ($options['fields'] as $kk => $field) {
                if (is_numeric($kk) && !is_bool($field)) {
                    $fields[] = $property.'.'.$field;
                } else {
                    if (true === $field) {
                        $fields[] = $property.'.'.$kk;
                    }
                }
            }
        } else {
            $fields[] = $property;
        }

        $document = $this->get($id, $fields, $options);
        $value    = $document;

        $extraFields = [];

        foreach (array_keys($options['fieldMapping']) as $kk) {
            if (!array_key_exists($kk, $value)) {
                continue;
            }
            $extraFields[$kk] = $value[$kk];
        }

        foreach (explode('.', $property) as $key) {
            if (!isset($value[$key])) {
                if (array_key_exists('default', $options)) {
                    return $options['default'];
                }
                throw $this->createRequiredException('doc.unknown_property/'.$this->getCollectionName(), str_replace('.', ' ', $property), $this->getCollectionName(), is_array($id) ? json_encode($id) : $id);
            }

            $value = $value[$key];
        }

        // @todo find a way to return the extra fields

        return $value;
    }
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
     * @throws Exception
     */
    public function getPropertyIfExist($id, $property, $defaultValue = null, $options = [])
    {
        return $this->getProperty($id, $property, ['default' => $defaultValue] + $options);
    }
    /**
     * Return the specified property as a list of the specified document.
     *
     * @param string|array $id
     * @param string       $property
     * @param array        $options
     *
     * @return mixed
     *
     * @throws Exception
     */
    public function getListProperty($id, $property, $options = [])
    {
        $value = $this->getProperty($id, $property, $options);

        if (!is_array($value)) {
            $value = [];
        }

        return $value;
    }
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
     * @throws Exception
     */
    public function getHashProperty($id, $property, $fields = [], $options = [])
    {
        $value = $this->getProperty($id, $property, ['fields' => $fields] + $options);

        if (!is_array($value)) {
            $value = [];
        }

        return $value;
    }
    /**
     * Test if specified property is present in specified document.
     *
     * @param string|array $id
     * @param string       $property
     * @param array        $options
     *
     * @return bool
     */
    public function hasProperty($id, $property, $options = [])
    {
        $document = $this->get($id, [$property], $options);
        $value    = $document;

        foreach (explode('.', $property) as $key) {
            if (!isset($value[$key])) {
                return false;
            }
            $value = $value[$key];
        }

        return true;
    }
    /**
     * Check if specified property is present in specified document.
     *
     * @param string|array $id
     * @param string       $property
     * @param array        $options
     *
     * @return $this
     *
     * @throws Exception
     */
    public function checkPropertyExist($id, $property, $options = [])
    {
        if (!$this->hasProperty($id, $property, $options)) {
            throw $this->createRequiredException('doc.unknown_property/'.$this->getCollectionName(), str_replace('.', ' ', $property), $this->getCollectionName(), is_array($id) ? json_encode($id) : $id);
        }

        return $this;
    }
    /**
     * Check if specified property is not present in specified document.
     *
     * @param string|array $id
     * @param string       $property
     * @param array        $options
     *
     * @return $this
     *
     * @throws Exception
     */
    public function checkPropertyNotExist($id, $property, $options = [])
    {
        if ($this->hasProperty($id, $property, $options)) {
            throw $this->createDuplicatedException('doc.exist_property/'.$this->getCollectionName(), str_replace('.', ' ', $property), $this->getCollectionName(), is_array($id) ? json_encode($id) : $id);
        }

        return $this;
    }
    /**
     * Create the specified index.
     *
     * @param array $index
     * @param array $options
     *
     * @return $this
     */
    public function createIndex($index, $options = [])
    {
        return $this->createIndexes([$index]);
    }
    /**
     * Create the specified indexes.
     *
     * @param array $indexes
     * @param array $options
     *
     * @return $this
     *
     * @throws Exception
     */
    public function createIndexes($indexes, $options = [])
    {
        foreach ($indexes as $index) {
            if (is_string($index)) {
                $index = ['field' => $index];
            }
            if (!is_array($index)) {
                throw $this->createMalformedException('doc.index.malformed/'.$this->getCollectionName());
            }
            $fields = $index['field'];
            unset($index['field']);
            $this->getDatabaseService()
                ->ensureIndex($this->getCollectionName(), $fields, $index, $options + $this->getConnectionOptions());
        }

        return $this;
    }
}
