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

use Itq\Common\Bag;
use Itq\Common\Model;
use Itq\Common\Traits;
use Itq\Common\ChunkedIterator;
use Itq\Common\Exception as CommonException;

/**
 * Document Service.
 *
 * @author itiQiti Dev Team <opensource@itiqiti.com>
 */
class DocumentService implements DocumentServiceInterface
{
    use Traits\ModelServiceTrait;
    use Traits\Document\HelperTrait;
    use Traits\Document\CreateServiceTrait;
    /**
     * @param array $data
     * @param array $options
     *
     * @return mixed
     *
     * @throws \Exception
     */
    public function ensureSameOrNotExistAndCreate(array $data, array $options = [])
    {
        throw $this->createNotYetImplementedException('feature.not_available', __METHOD__);
    }
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

        foreach ($this->getRepository()->find(['_id' => $ids], $fetchedFields, $options) as $k => $v) {
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
     * @return mixed
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
     * Retrieve the documents matching the specified criteria and return a page with total count.
     *
     * @param array    $criteria
     * @param array    $fields
     * @param null|int $limit
     * @param int      $offset
     * @param array    $sorts
     * @param array    $options
     *
     * @return mixed
     */
    public function findWithTotal($criteria = [], $fields = [], $limit = null, $offset = 0, $sorts = [], $options = [])
    {
        return [
            $this->find($criteria, $fields, $limit, $offset, $sorts, $options),
            $this->count($criteria, $options),
        ];
    }
    /**
     * Purge all the documents matching the specified criteria.
     *
     * @param array $criteria
     * @param array $options
     *
     * @return mixed
     */
    public function purge($criteria = [], $options = [])
    {
        $this->savePurge($criteria, $options);
        $this->event('purged');

        return $this;
    }
    /**
     * Replace all the specified documents.
     *
     * @param array $data
     * @param array $options
     *
     * @return mixed
     */
    public function replaceAll($data, $options = [])
    {
        $this->saveDeleteFound([], $options);
        $this->event('emptied');

        return $this->createBulk($data, $options);
    }
    /**
     * Replace all the specified documents.
     *
     * @param array $bulkData
     * @param array $options
     *
     * @return mixed
     *
     * @throws \Exception
     */
    public function replaceBulk($bulkData, $options = [])
    {
        $this->checkBulkData($bulkData, $options);

        $ids = [];

        foreach ($bulkData as $k => $v) {
            if (!isset($v['id'])) {
                throw $this->createRequiredException('Missing id for item #%s', $k);
            }
            $ids[] = $v['id'];
        }

        $this->deleteBulk($ids, $options);

        unset($ids);

        return $this->createBulk($bulkData, $options);
    }
    /**
     * Create a new document.
     *
     * @param mixed $data
     * @param array $settings
     * @param array $options
     *
     * @return mixed
     */
    public function import($data, $settings = [], $options = [])
    {
        $times                 = [];
        $times['start']        = microtime(true);
        $times['analyzeStart'] = microtime(true);

        list ($new, $existing, $removed, $unchanged) = $this->prepareImport($data, $settings, $options);

        $times['analyzeDuration'] = microtime(true) - $times['analyzeStart'];
        $count                    = count($data);

        unset($data);

        $errors = [
            'created' => ['count' => 0, 'details' => []],
            'updated' => ['count' => 0, 'details' => []],
            'deleted' => ['count' => 0, 'details' => []],
            'global'  => ['count' => 0, 'details' => []],
        ];

        $times['createBulkStart'] = microtime(true);
        $created                  = [];
        $notCreated               = [];

        if (count($new)) {
            try {
                $created    = $this->createBulk($new, ['returnId' => true] + $options);
                $notCreated = [];
            } catch (CommonException\BulkException $e) {
                $created    = $e->getSuccessData();
                $notCreated = $e->getErrorData();
                $errors['created']['count'] += $e->getExceptionCount();
                $errors['global']['count']  += $e->getExceptionCount();
                foreach ($e->getExceptions() as $exceptionKey => $exception) {
                    $errorDetails = ['code' => $exception->getCode(), 'message' => $exception->getMessage()];
                    if ($exception instanceof CommonException\FormValidationException) {
                        $errorDetails['validationMessages'] = $exception->getErrors();
                    }
                    $errors['created']['details'][$exceptionKey] = $errorDetails;
                }
            }
        }

        $times['createBulkDuration'] = microtime(true) - $times['createBulkStart'];

        unset($new);

        $times['updateBulkStart'] = microtime(true);

        $updated    = [];
        $notUpdated = [];
        if (count($existing)) {
            try {
                $updated    = $this->updateBulk($existing, ['returnId' => true] + $options);
                $notUpdated = [];
            } catch (CommonException\BulkException $e) {
                $updated    = $e->getSuccessData();
                $notUpdated = $e->getErrorData();
                $errors['updated']['count'] += $e->getExceptionCount();
                $errors['global']['count']  += $e->getExceptionCount();
                foreach ($e->getExceptions() as $exceptionKey => $exception) {
                    $errorDetails = ['code' => $exception->getCode(), 'message' => $exception->getMessage()];
                    if ($exception instanceof CommonException\FormValidationException) {
                        $errorDetails['validationMessages'] = $exception->getErrors();
                    }
                    $errors['updated']['details'][$exceptionKey] = $errorDetails;
                }
            }
        }

        $times['updateBulkDuration'] = microtime(true) - $times['updateBulkStart'];

        unset($existing);

        $times['deleteBulkStart'] = microtime(true);

        $deleted    = [];
        $notDeleted = [];
        if (count($removed)) {
            try {
                $deleted    = $this->deleteBulk($removed, ['returnId' => true] + $options);
                $notDeleted = [];
            } catch (CommonException\BulkException $e) {
                $deleted    = $e->getSuccessData();
                $notDeleted = $e->getErrorData();
                $errors['deleted']['count'] += $e->getExceptionCount();
                $errors['global']['count'] += $e->getExceptionCount();
                foreach ($e->getExceptions() as $exceptionKey => $exception) {
                    $errorDetails = ['code' => $exception->getCode(), 'message' => $exception->getMessage()];
                    if ($exception instanceof CommonException\FormValidationException) {
                        $errorDetails['validationMessages'] = $exception->getErrors();
                    }
                    $errors['deleted']['details'][$exceptionKey] = $errorDetails;
                }
            }
        }

        $times['deleteBulkDuration'] = microtime(true) - $times['deleteBulkStart'];

        unset($existing);

        $times['end']   = microtime(true);
        $times['total'] = $times['end'] - $times['start'];

        $createdCount    = count($created);
        $updatedCount    = count($updated);
        $deletedCount    = count($notDeleted);
        $notCreatedCount = count($notCreated);
        $notUpdatedCount = count($notUpdated);
        $notDeletedCount = count($notDeleted);
        $unchangedCount  = count($unchanged);

        $counts = [
            'created' => [
                'success' => ['value' => $createdCount, 'ratio' => round($createdCount / $count, 2)],
                'failure' => ['value' => $notCreatedCount, 'ratio' => round($notCreatedCount / $count, 2)],
            ],
            'updated' => [
                'success' => ['value' => $updatedCount, 'ratio' => round($updatedCount / $count, 2)],
                'failure' => ['value' => $notUpdatedCount, 'ratio' => round($notUpdatedCount / $count, 2)],
            ],
            'deleted' => [
                'success' => ['value' => $deletedCount, 'ratio' => round($deletedCount / $count, 2)],
                'failure' => ['value' => $notDeletedCount, 'ratio' => round($notDeletedCount / $count, 2)],
            ],
            'unchanged' => ['value' => $unchangedCount, 'ratio' => round($unchangedCount / $count, 2)],
            'analyzed'  => ['value' => $count, 'ratio' => 1.0],
        ];
        $counts += [
            'written' => [
                'value' => $counts['created']['success']['value'] + $counts['updated']['success']['value'] + $counts['deleted']['success']['value'],
                'ratio' => round(($counts['created']['success']['value'] + $counts['updated']['success']['value'] + $counts['deleted']['success']['value']) / $count, 2),
            ],
            'notWritten' => [
                'value' => ($count - $unchangedCount) - ($counts['created']['success']['value'] + $counts['updated']['success']['value'] + $counts['deleted']['success']['value']),
                'ratio' => round((($count - $unchangedCount) - ($counts['created']['success']['value'] + $counts['updated']['success']['value'] + $counts['deleted']['success']['value'])) / $count, 2),
            ],
        ];
        $statuses = [
            'created' => 0 === $errors['created']['count'] ? ($counts['created']['success']['value'] ? 'success' : 'not-processed') : 'failure',
            'updated' => 0 === $errors['updated']['count'] ? ($counts['updated']['success']['value'] ? 'success' : 'not-processed') : 'failure',
            'deleted' => 0 === $errors['deleted']['count'] ? ($counts['deleted']['success']['value'] ? 'success' : 'not-processed') : 'failure',
            'global'  => 0 === $errors['global']['count'] ? 'success' : 'failure',
        ];

        $result = new Model\Internal\ImportResult(
            [
                'statuses' => $statuses,
                'items'    => [
                    'created' => $created,
                    'updated' => $updated,
                    'deleted' => $deleted,
                    'notCreated' => array_keys($notCreated),
                    'notUpdated' => array_keys($notUpdated),
                    'notDeleted' => array_keys($notDeleted),
                    'unchanged' => $unchanged,
                ],
                'counts' => $counts,
                'tops'   => [
                    'start' => (new \DateTime('@'.round($times['start'], 0)))->format('c'),
                    'startAnalyze' => (new \DateTime('@'.round($times['analyzeStart'], 0)))->format('c'),
                    'startCreate' => (new \DateTime('@'.round($times['createBulkStart'], 0)))->format('c'),
                    'startUpdate' => (new \DateTime('@'.round($times['updateBulkStart'], 0)))->format('c'),
                    'startDelete' => (new \DateTime('@'.round($times['deleteBulkStart'], 0)))->format('c'),
                    'end' => (new \DateTime('@'.round($times['end'], 0)))->format('c'),
                ],
                'errors' => $errors,
                'durations' => [
                    'analyze' => [
                        'value' => round($times['analyzeDuration'] * 1000, 0),
                        'ratio' => round($times['analyzeDuration'] / $times['total'], 2),
                    ],
                    'create'  => [
                        'value' => round($times['createBulkDuration'] * 1000, 0),
                        'ratio' => round($times['createBulkDuration'] / $times['total'], 2),
                    ],
                    'update'  => [
                        'value' => round($times['updateBulkDuration'] * 1000, 0),
                        'ratio' => round($times['updateBulkDuration'] / $times['total'], 2),
                    ],
                    'delete'  => [
                        'value' => round($times['deleteBulkDuration'] * 1000, 0),
                        'ratio' => round($times['deleteBulkDuration'] / $times['total'], 2),
                    ],
                    'total' => [
                        'value' => round($times['total'] * 1000, 0),
                        'ratio' => 1.0,
                    ],
                ],
                'speeds' => [
                    'global' => [
                        'value' => $times['total'] ? round(($counts['written']['value'] + $counts['notWritten']['value']) / $times['total'], 0) : 0,
                        'unit' => 'op/s',
                    ],
                    'create' => [
                        'value' => $times['createBulkDuration'] ? round($counts['created']['success']['value'] / $times['createBulkDuration'], 0) : 0,
                        'unit' => 'op/s',
                    ],
                    'update' => [
                        'value' => $times['updateBulkDuration'] ? round($counts['updated']['success']['value'] / $times['updateBulkDuration'], 0) : 0,
                        'unit' => 'op/s',
                    ],
                    'delete' => [
                        'value' => $times['deleteBulkDuration'] ? round($counts['deleted']['success']['value'] / $times['deleteBulkDuration'], 0) : 0,
                        'unit' => 'op/s',
                    ],
                    'analyze' => [
                        'value' => $times['analyzeDuration'] ? round($count / $times['analyzeDuration'], 0) : 0,
                        'unit' => 'op/s',
                    ],
                ],
            ]
        );

        if (isset($settings['progressToken'])) {
            $result->progressToken = $settings['progressToken'];
        }

        $this->applyBusinessRules('complete_import', $result, $options);
        $this->event('imported', $result);

        return $result;
    }
    /**
     * @param string $id
     * @param array  $data
     * @param array  $options
     *
     * @return mixed
     */
    public function update($id, $data, $options = [])
    {
        list($doc, $array, $old, $transitions) = $this->prepareUpdate($id, $data, $options);

        unset($data);

        $this->saveUpdate($id, $this->enrichUpdates($array, $doc, $options), $options);

        return $this->completeUpdate($id, $doc, $array, $old, $transitions, $options);
    }
    /**
     * @param mixed $fieldName
     * @param mixed $fieldValue
     * @param array $data
     * @param array $options
     *
     * @return mixed
     */
    public function updateBy($fieldName, $fieldValue, $data, $options = [])
    {
        return $this->update($this->getBy($fieldName, $fieldValue, ['id'], $options)->id, $data, $options);
    }
    /**
     * @param array $bulkData
     * @param array $options
     *
     * @return mixed
     */
    public function updateBulk($bulkData, $options = [])
    {
        $this->checkBulkData($bulkData, $options);

        $docs        = [];
        $arrays      = [];
        $olds        = [];
        $transitions = [];
        $idMatch     = [];
        $docIds      = [];
        $errors      = ['prepare' => [], 'complete' => [], 'saved' => null];

        foreach ($bulkData as $i => $data) {
            $id = $data['id'];
            unset($data['id']);
            try {
                list($docs[$i], $arrays[$id], $olds[$i], $transitions[$i]) = $this->prepareUpdate($id, $data, $options);
                $arrays[$id]  = $this->enrichUpdates($arrays[$id], $docs[$i], $options);
                $docIds[$i]   = $id;
                $idMatch[$id] = $i;
            } catch (\Exception $e) {
                $errors['prepare'][$i] = ['data' => $data, 'exception' => $e];
            }
            unset($bulkData[$i]);
        }

        unset($bulkData);

        $saved = [];

        if (count($arrays)) {
            try {
                $this->saveUpdateBulk($arrays, $options);
                $saved = $arrays;
            } catch (\Exception $e) {
                $errors['saved'] = ['exception' => $e];
                $saved = [];
            }
        }

        $completedDocs   = [];
        $completedDocsId = [];

        foreach ($saved as $id => $array) {
            unset($arrays[$id]);
            $i = $idMatch[$id];
            try {
                $completedDocs[$i] = $this->completeUpdate($id, $docs[$i], $array, $olds[$i], $transitions[$i], $options);
                $completedDocsId[$i] = $id;
                unset($docs[$i]);
            } catch (\Exception $e) {
                $errors['complete'][$i] = ['doc' => $docs[$i], 'data' => $array, 'exception' => $e, 'id' => $id];
            }
            unset($olds[$i], $transitions[$i]);
        }

        $exceptions = [];
        $failedDocs = [];

        foreach ($errors['prepare'] as $i => $error) {
            $exceptions[$i] = $error['exception'];
            $failedDocs[$i] = $error['data'];
        }
        if (null !== $errors['saved']) {
            $exceptions['.saved'] = $errors['saved']['exception'];
        }
        foreach ($errors['complete'] as $i => $error) {
            $exceptions[$i] = $error['exception'];
            $failedDocs[$i] = $error['id'];
        }
        if (count($exceptions)) {
            throw new CommonException\BulkException($exceptions, $failedDocs, $completedDocsId);
        }

        unset($olds);
        unset($arrays);

        return (isset($options['returnId']) && true === $options['returnId']) ? $completedDocsId : $completedDocs;
    }
    /**
     * Increment the specified property of the specified document.
     *
     * @param mixed        $id
     * @param string|array $property
     * @param int          $value
     * @param array        $options
     *
     * @return $this
     */
    public function increment($id, $property, $value = 1, $options = [])
    {
        if (is_array($property)) {
            $this->saveIncrementProperties($id, $property, $options);
        } else {
            $this->saveIncrementProperty($id, $property, $value, $options);
        }

        return $this;
    }
    /**
     * Increment the specified property of the specified document.
     *
     * @param string       $fieldName
     * @param mixed        $fieldValue
     * @param string|array $property
     * @param int          $value
     * @param array        $options
     *
     * @return $this
     */
    public function incrementBy($fieldName, $fieldValue, $property, $value = 1, $options = [])
    {
        if (is_array($property)) {
            $this->saveIncrementProperties([$fieldName => $fieldValue], $property, $options);
        } else {
            $this->saveIncrementProperty([$fieldName => $fieldValue], $property, $value, $options);
        }

        return $this;
    }
    /**
     * Decrement the specified property of the specified document.
     *
     * @param string       $fieldName
     * @param mixed        $fieldValue
     * @param string|array $property
     * @param int          $value
     * @param array        $options
     *
     * @return $this
     */
    public function decrementBy($fieldName, $fieldValue, $property, $value = 1, $options = [])
    {
        return $this->incrementBy($fieldName, $fieldValue, $property, -$value, $options);
    }
    /**
     * Decrement the specified property of the specified document.
     *
     * @param mixed        $id
     * @param string|array $property
     * @param int          $value
     * @param array        $options
     *
     * @return $this
     */
    public function decrement($id, $property, $value = 1, $options = [])
    {
        if (is_array($property)) {
            $this->saveDecrementProperties($id, $property, $options);
        } else {
            $this->saveDecrementProperty($id, $property, $value, $options);
        }

        return $this;
    }
    /**
     * Delete the specified document.
     *
     * @param mixed $id
     * @param array $options
     *
     * @return mixed
     *
     * @throws \Exception
     */
    public function delete($id, $options = [])
    {
        list($old) = $this->prepareDelete($id, $options);

        $this->saveDelete($id, $options);

        return $this->completeDelete($id, $old, $options);
    }
    /**
     * Delete the specified documents.
     *
     * @param array $ids
     * @param array $options
     *
     * @return mixed
     */
    public function deleteBulk($ids, $options = [])
    {
        $this->checkBulkData($ids, $options);

        $olds     = [];
        $deleteds = [];

        foreach ($ids as $id) {
            list($olds[$id]) = $this->prepareDelete($id, $options);
        }


        foreach ($this->saveDeleteBulk($ids, $options) as $id) {
            $deleteds[$id] = $this->completeDelete($id, $olds[$id], $options);
            unset($olds[$id]);
        }

        unset($ids);
        unset($olds);

        return $deleteds;
    }
    /**
     * Create document if not exist or update it.
     *
     * @param mixed $data
     * @param array $options
     *
     * @return mixed
     */
    public function createOrUpdate($data, $options = [])
    {
        if (isset($data['id']) && $this->has($data['id'])) {
            $id = $data['id'];
            unset($data['id']);

            return $this->update($id, $data, $options);
        }

        return $this->create($data, $options);
    }
    /**
     * Create documents if not exist or update them.
     *
     * @param mixed $bulkData
     * @param array $options
     *
     * @return mixed
     */
    public function createOrUpdateBulk($bulkData, $options = [])
    {
        $this->checkBulkData($bulkData, $options);

        $toCreate = [];
        $toUpdate = [];

        foreach ($bulkData as $i => $data) {
            unset($bulkData[$i]);
            if (isset($data['id']) && $this->has($data['id'])) {
                $toUpdate[$i] = $data;
            } else {
                $toCreate[$i] = $data;
            }
        }

        unset($bulkData);

        $docs = [];

        if (count($toCreate)) {
            $docs += $this->createBulk($toCreate, $options);
        }

        unset($toCreate);

        if (count($toUpdate)) {
            $docs += $this->updateBulk($toUpdate, $options);
        }

        unset($toUpdate);

        return $docs;
    }
    /**
     * Create documents if not exist or delete them.
     *
     * @param mixed $bulkData
     * @param array $options
     *
     * @return mixed
     */
    public function createOrDeleteBulk($bulkData, $options = [])
    {
        $this->checkBulkData($bulkData, $options);

        $toCreate = [];
        $toDelete = [];

        foreach ($bulkData as $i => $data) {
            if (isset($data['id']) && $this->has($data['id'])) {
                $toDelete[$i] = $data;
            } else {
                $toCreate[$i] = $data;
            }
            unset($bulkData[$i]);
        }

        unset($bulkData);

        $docs = [];

        if (count($toCreate)) {
            $docs += $this->createBulk($toCreate, $options);
        }

        unset($toCreate);

        if (count($toDelete)) {
            $docs += $this->deleteBulk($toDelete, $options);
        }

        unset($toDelete);

        return $docs;
    }
    /**
     * @param string $id
     * @param string $key
     * @param int    $value
     * @param array  $options
     *
     * @return $this
     */
    public function incrementStat($id, $key, $value = 1, array $options = [])
    {
        return $this->incrementStats($id, [$key => $value], $options);
    }
    /**
     * @param string $id
     * @param array  $stats
     * @param array  $options
     *
     * @return $this
     */
    public function incrementStats($id, array $stats, array $options = [])
    {
        if (!count($stats)) {
            return $this;
        }

        $options += ['suffix' => null, 'prefix' => null];

        $data = [];

        foreach ($stats as $k => $v) {
            $data['stats.'.$options['prefix'].preg_replace('/[^a-z0-9_\:\-]+/i', '_', $k).$options['suffix']] = $v;
            unset($stats[$k]);
        }

        unset($stats);

        $this->getRepository()->alter($id, ['$inc' => $data]);

        unset($data);

        return $this;
    }
    /**
     * @param string $id
     * @param array  $stats
     * @param array  $options
     *
     * @return $this
     */
    public function setStats($id, array $stats, array $options = [])
    {
        if (!count($stats)) {
            return $this;
        }

        $options += ['suffix' => null, 'prefix' => null];

        $data = [];

        foreach ($stats as $k => $v) {
            $data['stats.'.$options['prefix'].preg_replace('/[^a-z0-9_\:\-]+/i', '_', $k).$options['suffix']] = $v;
            unset($stats[$k]);
        }

        unset($stats);

        $this->getRepository()->alter($id, ['$set' => $data]);

        unset($data);

        return $this;
    }
    /**
     * @param string $id
     * @param array  $hasTags
     * @param array  $hasNotTags
     *
     * @return $this
     *
     * MongoDB currently (3.0.x) not support $addToSet and $pull on the same field in the same request
     */
    public function ensureTags($id, array $hasTags = [], array $hasNotTags = [])
    {
        $updates1 = [];
        $updates2 = [];

        if (count($hasTags)) {
            $updates1['$addToSet'] = ['tags' => ['$each' => array_values($hasTags)]];
        }
        if (count($hasNotTags)) {
            $updates2['$pull'] = ['tags' => ['$in' => array_values($hasNotTags)]];
        }
        if (count($updates1)) {
            $this->getRepository()->alter($id, $updates1, ['multiple' => true]);
        }
        if (count($updates2)) {
            $this->getRepository()->alter($id, $updates2, ['multiple' => true]);
        }

        return $this;
    }
    /**
     * @param string $id
     * @param array  $options
     *
     * @return array
     */
    public function getTags($id, array $options = [])
    {
        $tags = null;

        if (isset($options['doc'])) {
            $doc = $options['doc'];
            if (property_exists($doc, 'tags')) {
                $tags = $doc->tags;
            }
        }
        if (!is_array($tags)) {
            $doc = $this->getRepository()->get($id, ['tags']);
            $tags = isset($doc['tags']) ? $doc['tags'] : null;
        }
        if (!is_array($tags) || !count($tags)) {
            $tags = [];
        }

        return $tags;
    }
    /**
     * @param string $id
     * @param string $prefix
     * @param array  $options
     *
     * @return array
     */
    public function getTagCodes($id, $prefix, array $options = [])
    {
        $values = [];
        $prefixLength = strlen($prefix);
        foreach ($this->getTags($id, $options) as $tag) {
            if (substr($tag, 0, $prefixLength) !== $prefix) {
                continue;
            }
            $values[strtolower(substr($tag, $prefixLength))] = true;
        }

        return array_keys($values);
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
     * @param mixed $id
     * @param array $options
     *
     * @return array
     */
    protected function prepareDelete($id, $options = [])
    {
        $old = $this->get($id, [], $options);

        $this->restrictModel($old, ['operation' => 'delete'] + $options);
        $this->applyBusinessRules('delete', $old, $options);

        return [$old];
    }
    /**
     * @param mixed $id
     * @param mixed $old
     * @param array $options
     *
     * @return mixed
     */
    protected function completeDelete($id, $old, $options = [])
    {
        $this->cleanModel($old, ['operation' => 'delete'] + $options);
        $this->applyBusinessRules('complete_delete', $old, $options);
        $this->event('deleted', $old);

        unset($old);

        return ['id' => $id, 'status' => 'deleted'];
    }
    /**
     * @param mixed $id
     * @param array $data
     * @param array $options
     *
     * @return array
     */
    protected function prepareUpdate($id, $data = [], $options = [])
    {
        $options += ['id' => $id];

        $doc                           = $this->validateData($data, 'update', ['clearMissing' => false] + $options);
        $old                           = null;
        $hasWorkflows                  = false;
        $activeWorkflowsRequiredFields = [];

        if ($this->hasActiveWorkflows($doc, $options)) {
            $hasWorkflows = true;
            $activeWorkflowsRequiredFields = $this->getActiveWorkflowsRequiredFields($doc, $options);
        }

        if (true === $hasWorkflows || $this->observed('updated_old') || $this->observed('updated_full_old')) {
            $old = $this->get($id, array_unique(array_merge($activeWorkflowsRequiredFields, array_keys($data))), $options);
        }

        unset($data, $activeWorkflowsRequiredFields);

        $doc = $this->refreshModel($doc, ['operation' => 'update', 'populateNulls' => false, 'id' => $id] + $options);

        $this->applyBusinessRules('update', $doc, $options);

        $transitions = [];

        if ($hasWorkflows) {
            $transitions = $this->applyActiveWorkflows($doc, $old, $options);
            if (is_array($transitions)) {
                foreach ($transitions as $transition) {
                    $this->applyBusinessRules('update.'.$transition, $doc, $options);
                }
            }
        }

        return [$doc, $this->convertToArray($doc, $options), $old, $transitions];
    }
    /**
     * @param mixed $id
     * @param mixed $doc
     * @param array $array
     * @param mixed $old
     * @param array $transitions
     * @param array $options
     *
     * @return mixed
     */
    protected function completeUpdate($id, $doc, $array, $old, $transitions = [], $options = [])
    {
        $options += ['id' => $id];

        if (property_exists($doc, 'id') && null === $doc->id) {
            $doc->id = (string) $id;
        }

        unset($array);

        $doc = $this->cleanModel($doc, ['operation' => 'update'] + $options);

        $this->applyBusinessRules('complete_update', $doc, $options);

        foreach ($transitions as $transition) {
            $this->applyBusinessRules('complete_update.'.$transition, $doc, $options);
        }

        $this->event('updated', $doc);

        foreach ($transitions as $transition) {
            $this->event('updated.'.$transition, $doc);
        }

        unset($old);

        return $doc;
    }
    /**
     * @param array $array
     * @param array $options
     *
     * @return mixed|void
     */
    protected function saveCreate(array $array, array $options = [])
    {
        $this->getRepository()->create($array, $options);
    }
    /**
     * @param array $arrays
     * @param array $options
     *
     * @return array
     */
    protected function saveCreateBulk(array $arrays, array $options = [])
    {
        return $this->getRepository()->createBulk($arrays, $options);
    }
    /**
     * @param string $id
     * @param array  $array
     * @param array  $options
     *
     * @return mixed|void
     */
    protected function saveUpdate($id, array $array, array $options)
    {
        $this->getRepository()->update($id, $array, $options);
    }
    /**
     * @param array $arrays
     * @param array $options
     *
     * @return array
     */
    protected function saveUpdateBulk(array $arrays, array $options)
    {
        return $this->getRepository()->updateBulk($arrays, $options);
    }
    /**
     * @param string $id
     * @param string $property
     * @param mixed  $value
     * @param array  $options
     */
    protected function saveIncrementProperty($id, $property, $value, array $options)
    {
        $this->getRepository()->incrementProperty($id, $property, $value, $options);
    }
    /**
     * @param string $id
     * @param array  $properties
     * @param array  $options
     */
    protected function saveIncrementProperties($id, $properties, array $options)
    {
        $this->getRepository()->incrementProperties($id, $properties, $options);
    }
    /**
     * @param string $id
     * @param string $property
     * @param mixed  $value
     * @param array  $options
     */
    protected function saveDecrementProperty($id, $property, $value, array $options)
    {
        $this->getRepository()->decrementProperty($id, $property, $value, $options);
    }
    /**
     * @param string $id
     * @param array  $properties
     * @param array  $options
     */
    protected function saveDecrementProperties($id, array $properties, array $options)
    {
        $this->getRepository()->decrementProperties($id, $properties, $options);
    }
    /**
     * @param array $criteria
     * @param array $options
     *
     * @return mixed|void
     */
    protected function savePurge(array $criteria = [], array $options = [])
    {
        $this->getRepository()->deleteFound($criteria, $options);
    }
    /**
     * @param array $criteria
     * @param array $options
     */
    protected function saveDeleteFound(array $criteria, array $options)
    {
        $this->getRepository()->deleteFound($criteria, $options);
    }
    /**
     * @param string $id
     * @param array  $options
     *
     * @return mixed|void
     */
    protected function saveDelete($id, array $options)
    {
        $this->getRepository()->delete($id, $options);
    }
    /**
     * @param array $ids
     * @param array $options
     *
     * @return array
     */
    protected function saveDeleteBulk($ids, array $options)
    {
        return $this->getRepository()->deleteBulk($ids, $options);
    }
    /**
     * @param array $data
     * @param array $settings
     * @param array $options
     *
     * @return array
     *
     * @throws \Exception
     */
    protected function prepareImport($data, $settings, $options = [])
    {
        $settings += ['keys' => [], 'fields' => [], 'common' => []];
        $doc       = new Bag(['data' => $data, 'settings' => $settings]);

        unset($data);
        unset($settings);

        $this->applyBusinessRules('import', $doc, $options);

        $settings = $doc->get('settings');
        $data     = $doc->get('data');
        $keys     = $settings['keys'];
        $fields   = $settings['fields'];
        $common   = $settings['common'];

        if (!count($keys)) {
            throw $this->createRequiredException('doc.required_keys');
        }

        if (!is_array($keys)) {
            $keys = [$keys];
        }

        $criteria        = [];
        $actualHashs     = [];
        $existingHashs   = [];
        $removedHashs    = [];
        $modifiedHashs   = [];
        $unmodifiedHashs = [];

        foreach ($data as $k => $item) {
            if (!is_array($item)) {
                $item = [];
            }
            $item += $common;
            $criteria[] = $this->computeItemCriteria($item, $keys);
            $actualHashs[$this->computeItemHash($item, $keys)] = ['item' => $item, 'key' => $k];
            unset($data[$k], $k, $item);
        }

        $that            = $this;
        $requestedFields = $this->computeKeyFields($keys);
        $trackedFields   = array_unique(array_merge($requestedFields, $fields));
        $docCallback     = function ($doc) use (&$existingHashs, $actualHashs, $that, $keys, $trackedFields) {
            $item = [];
            foreach ($trackedFields as $field) {
                $item[$field] = isset($doc->$field) ? $doc->$field : null;
            }
            $item['id'] = (string) $doc->id;
            unset($item['_id']);
            $existingHashs[$that->computeItemHash($item, $keys)] = $item;

            return $doc;
        };

        $this->find(['$or' => $criteria], $trackedFields, null, 0, [], ['docCallback' => $docCallback, 'noReturn' => true]);

        $new       = [];
        $updated   = [];
        $deleted   = [];
        $unchanged = [];

        foreach (array_diff_key($actualHashs, $existingHashs) as $k => $v) {
            $new[$actualHashs[$k]['key']] = $v['item'];
        }
        foreach (array_intersect_key($actualHashs, $existingHashs) as $k => $v) {
            if ($this->computeItemHash($v['item'], $trackedFields) === $this->computeItemHash($existingHashs[$k], $trackedFields)) {
                $unmodifiedHashs[$k] = $existingHashs[$k]['id'];
            } else {
                $modifiedHashs[$k] = $existingHashs[$k];
            }
        }
        foreach ($modifiedHashs as $k => $v) {
            $updated[$actualHashs[$k]['key']] = ['id' => $v['id']] + $actualHashs[$k]['item'];
        }
        foreach ($removedHashs as $k => $v) {
            $deleted[$actualHashs[$k]['key']] = $v;
        }
        foreach ($unmodifiedHashs as $k => $v) {
            $unchanged[$actualHashs[$k]['key']] = $v;
        }

        return [$new, $updated, $deleted, $unchanged];
    }
    /**
     * @param array $item
     * @param array $key
     *
     * @return array
     */
    protected function computeItemCriteria($item, $key)
    {
        $criteria = [];

        foreach ($key as $field) {
            $criteria[$field] = isset($item[$field]) ? $item[$field] : null;
        }

        return $criteria;
    }
    /**
     * @param array $item
     * @param array $key
     *
     * @return string
     */
    protected function computeItemHash($item, $key)
    {
        $hashTokens = [];

        foreach ($key as $field) {
            $hashTokens[] = $field.':'.(isset($item[$field]) ? $item[$field] : null);
        }

        return sha1(join('|', $hashTokens));
    }
    /**
     * @param array $key
     *
     * @return array
     */
    protected function computeKeyFields($key)
    {
        $fields = [];

        $found = false;

        foreach ($key as $field) {
            if ('id' === $field || '_id' === $field) {
                $fields[] = '_id';
                $found = true;
            } else {
                $fields[] = $field;
            }
        }

        if (!$found) {
            $fields[] = '_id';
        }

        return $fields;
    }
    /**
     * @param string $id
     * @param string $property
     * @param bool   $value
     *
     * @return $this
     */
    protected function markProperty($id, $property, $value = true)
    {
        return $this->setProperty($id, $property, (bool) $value);
    }
    /**
     * @param string $id
     * @param string $property
     * @param mixed  $value
     *
     * @return $this
     */
    protected function setProperty($id, $property, $value)
    {
        $this->getRepository()->setProperty($id, $property, $value);

        return $this;
    }
}
