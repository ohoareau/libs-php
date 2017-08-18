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

use Itq\Common\Bag;
use Itq\Common\Model;
use Itq\Common\Exception;

/**
 * @author itiQiti Dev Team <opensource@itiqiti.com>
 */
trait ImportServiceTrait
{
    /**
     * Import data.
     *
     * @param mixed $data
     * @param array $settings
     * @param array $options
     *
     * @return Model\Internal\ImportResult
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
            } catch (Exception\BulkException $e) {
                $created    = $e->getSuccessData();
                $notCreated = $e->getErrorData();
                $errors['created']['count'] += $e->getExceptionCount();
                $errors['global']['count']  += $e->getExceptionCount();
                foreach ($e->getExceptions() as $exceptionKey => $exception) {
                    $errorDetails = ['code' => $exception->getCode(), 'message' => $exception->getMessage()];
                    if ($exception instanceof Exception\FormValidationException) {
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
            } catch (Exception\BulkException $e) {
                $updated    = $e->getSuccessData();
                $notUpdated = $e->getErrorData();
                $errors['updated']['count'] += $e->getExceptionCount();
                $errors['global']['count']  += $e->getExceptionCount();
                foreach ($e->getExceptions() as $exceptionKey => $exception) {
                    $errorDetails = ['code' => $exception->getCode(), 'message' => $exception->getMessage()];
                    if ($exception instanceof Exception\FormValidationException) {
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
            } catch (Exception\BulkException $e) {
                $deleted    = $e->getSuccessData();
                $notDeleted = $e->getErrorData();
                $errors['deleted']['count'] += $e->getExceptionCount();
                $errors['global']['count'] += $e->getExceptionCount();
                foreach ($e->getExceptions() as $exceptionKey => $exception) {
                    $errorDetails = ['code' => $exception->getCode(), 'message' => $exception->getMessage()];
                    if ($exception instanceof Exception\FormValidationException) {
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
}
