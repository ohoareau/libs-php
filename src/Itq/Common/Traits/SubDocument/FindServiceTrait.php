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
 * Find service trait.
 *
 * @author itiQiti Dev Team <opensource@itiqiti.com>
 */
trait FindServiceTrait
{
    /**
     * Count documents matching the specified criteria.
     *
     * @param string $parentId
     * @param mixed  $criteria
     * @param array  $options
     *
     * @return mixed
     */
    public function count($parentId, $criteria = [], $options = [])
    {
        if (!$this->getRepository()->hasProperty($parentId, $this->getRepoKey(), $options)) {
            return 0;
        }

        $items = $this->getRepository()->getListProperty($parentId, $this->getRepoKey(), $options);

        $this->filterItems($items, $criteria);

        unset($criteria);

        return count($items);
    }
    /**
     * Retrieve the documents matching the specified criteria.
     *
     * @param string   $parentId
     * @param array    $criteria
     * @param array    $fields
     * @param null|int $limit
     * @param int      $offset
     * @param array    $sorts
     * @param array    $options
     *
     * @return mixed
     */
    public function find($parentId, $criteria = [], $fields = [], $limit = null, $offset = 0, $sorts = [], $options = [])
    {
        if (!$this->getRepository()->hasProperty($parentId, $this->getRepoKey())) {
            return [];
        }

        $items         = $this->getRepository()->getListProperty($parentId, $this->getRepoKey());
        $fetchedFields = $this->prepareFields($fields);

        $this->sortItems($items, $sorts, $options);
        $this->filterItems($items, $criteria, $fetchedFields, null, $options);
        $this->paginateItems($items, $limit, $offset, $options);

        foreach ($items as $k => $v) {
            $items[$k] = $this->convertToModel($v, ['requestedFields' => $fields, 'fetchedFields' => $fetchedFields, 'parentId' => $parentId, 'operation' => 'retrieve'] + $options);
        }

        return $items;
    }
    /**
     * @param mixed $parentId
     * @param array $criteria
     * @param array $fields
     * @param int   $offset
     * @param array $sorts
     * @param array $options
     *
     * @return mixed|null
     */
    public function findOne($parentId, $criteria = [], $fields = [], $offset = 0, $sorts = [], $options = [])
    {
        $items = $this->find($parentId, $criteria, $fields, 1, $offset, $sorts, $options);

        if (!count($items)) {
            return null;
        }

        return array_shift($items);
    }
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
     * @return mixed
     */
    public function findBy($fieldName, $fieldValue, $criteria = [], $fields = [], $limit = null, $offset = 0, $sorts = [], $options = [])
    {
        return $this->find(
            $this->getRepository()->getBy($fieldName, $fieldValue, ['_id'])['_id'],
            $criteria,
            $fields,
            $limit,
            $offset,
            $sorts,
            $options
        );
    }
    /**
     * Retrieve the documents matching the specified criteria and return a page with total count.
     *
     * @param string   $parentId
     * @param array    $criteria
     * @param array    $fields
     * @param null|int $limit
     * @param int      $offset
     * @param array    $sorts
     * @param array    $options
     *
     * @return mixed
     */
    public function findWithTotal($parentId, $criteria = [], $fields = [], $limit = null, $offset = 0, $sorts = [], $options = [])
    {
        return [
            $this->find($parentId, $criteria, $fields, $limit, $offset, $sorts, $options),
            $this->count($parentId, $criteria, $options),
        ];
    }
    /**
     * @return RepositoryInterface
     */
    abstract public function getRepository();
}
