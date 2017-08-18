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

use Itq\Common\ChunkedIterator;
use Itq\Common\RepositoryInterface;

/**
 * @author itiQiti Dev Team <opensource@itiqiti.com>
 */
trait FindServiceTrait
{
    /**
     * Count documents matching the specified criteria.
     *
     * @param mixed $criteria
     * @param array $options
     *
     * @return int
     */
    public function count($criteria = [], $options = [])
    {
        return $this->getRepository()->count($criteria, $options);
    }
    /**
     * Retrieve the documents matching the specified criteria.
     *
     * @param array    $criteria
     * @param array    $fields
     * @param null|int $limit
     * @param int      $offset
     * @param array    $sorts
     * @param array    $options
     *
     * @return array
     */
    public function find($criteria = [], $fields = [], $limit = null, $offset = 0, $sorts = [], $options = [])
    {
        $options      += ['docCallback' => null, 'chunkSize' => 500, 'noReturn' => false];
        $fetchedFields = $this->prepareFields($fields);
        $data          = true !== $options['noReturn'] ? [] : null;
        $criteria      = $this->prepareCriteria($criteria);
        $that          = $this;
        $repo          = $this->getRepository();
        $offset        = is_array($offset) ? 0 : $offset;
        $iterator      = new ChunkedIterator(
            function ($loopLimit, $localOffset) use ($repo, &$criteria, &$fetchedFields, $offset, &$sorts, &$options) {
                return $repo->find($criteria, $fetchedFields, $loopLimit, $offset + $localOffset, $sorts, $options);
            },
            $options['chunkSize'],
            $limit,
            function ($v, $k) use (&$fields, &$fetchedFields, &$options, $that) {
                $doc         = $that->convertToModel($v, ['docId' => $k, 'requestedFields' => $fields, 'fetchedFields' => $fetchedFields, 'operation' => 'retrieve'] + $options);
                $docCallback = $options['docCallback'];

                return $docCallback ? $docCallback($doc) : $doc;
            }
        );

        foreach ($iterator as $loopResults) {
            if (true === $options['noReturn']) {
                continue;
            }
            $data = array_merge($data, $loopResults);
        }

        unset($criteria, $limit, $offset, $sorts, $options, $fields, $fetchedFields);

        return $data;
    }
    /**
     * @param array $criteria
     * @param array $fields
     * @param int   $offset
     * @param array $sorts
     * @param array $options
     *
     * @return mixed|null
     */
    public function findOne($criteria = [], $fields = [], $offset = 0, $sorts = [], $options = [])
    {
        $items = $this->find($criteria, $fields, 1, $offset, $sorts, $options);

        if (!count($items)) {
            return null;
        }

        return array_shift($items);
    }
    /**
     * @param array $data
     * @param array $fields
     * @param array $options
     *
     * @return mixed
     */
    public function findOneByData($data, array $fields = [], array $options = [])
    {
        return $this->findOne($data, $fields, 0, [], $options);
    }
    /**
     * Retrieve the documents matching the specified criteria and return a page with total count.
     *
     * @param array    $criteria
     * @param array    $fields
     * @param null|int $limit
     * @param int      $offset
     * @param array    $sorts
     * @param array    $options
     *
     * @return array
     */
    public function findWithTotal($criteria = [], $fields = [], $limit = null, $offset = 0, $sorts = [], $options = [])
    {
        return [
            $this->find($criteria, $fields, $limit, $offset, $sorts, $options),
            $this->count($criteria, $options),
        ];
    }
    /**
     * @return RepositoryInterface
     */
    abstract public function getRepository();
}
