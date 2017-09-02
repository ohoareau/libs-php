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

use Exception;
use Itq\Common\Exception as CommonException;

/**
 * @author itiQiti Dev Team <opensource@itiqiti.com>
 */
trait CreateServiceTrait
{
    /**
     * Create a new document.
     *
     * @param mixed $data
     * @param array $options
     *
     * @return mixed
     */
    public function create($data, $options = [])
    {
        list($doc, $array) = $this->prepareCreate($data, $options);

        unset($data);

        $this->saveCreate($array, $options);

        return $this->completeCreate($doc, $array, $options);
    }
    /**
     * Create a list of documents.
     *
     * @param mixed $bulkData
     * @param array $options
     *
     * @return mixed
     */
    public function createBulk($bulkData, $options = [])
    {
        $this->checkBulkData($bulkData, $options);

        $docs   = [];
        $arrays = [];
        $errors = ['prepare' => [], 'complete' => [], 'saved' => null];

        foreach ($bulkData as $i => $data) {
            try {
                list($docs[$i], $arrays[$i]) = $this->prepareCreate($data, $options);
            } catch (Exception $e) {
                $errors['prepare'][$i] = ['data' => $data, 'exception' => $e];
            }
            unset($bulkData[$i]);
        }

        unset($bulkData);

        $saved = [];

        if (count($arrays)) {
            try {
                $this->saveCreateBulk($arrays, $options);
                $saved = $arrays;
            } catch (Exception $e) {
                $errors['saved'] = ['exception' => $e];
                $saved = [];
            }
        }

        $completedDocs   = [];
        $completedDocsId = [];

        foreach ($saved as $i => $array) {
            unset($arrays[$i]);
            try {
                $completedDocs[$i] = $this->completeCreate($docs[$i], $array, $options);
                $completedDocsId[$i] = (string) $array['_id'];
                unset($docs[$i]);
            } catch (Exception $e) {
                $errors['complete'][$i] = ['doc' => $docs[$i], 'data' => $array, 'exception' => $e];
            }
        }

        $exceptions = [];
        $failedDocs = [];

        foreach ($errors['prepare'] as $i => $error) {
            $exceptions[$i] = $error['exception'];
            $failedDocs[$i] = $error['data'];
        }
        if (null !== $errors['saved']) {
            $exceptions['saved'] = $errors['saved']['exception'];
        }
        foreach ($errors['complete'] as $i => $error) {
            $exceptions[$i] = $error['exception'];
            $failedDocs[$i] = (string) $error['data']['_id'];
        }
        if (count($exceptions)) {
            throw new CommonException\BulkException($exceptions, $failedDocs, $completedDocsId);
        }

        return (isset($options['returnId']) && true === $options['returnId']) ? $completedDocsId : $completedDocs;
    }
    /**
     * @param array $data
     * @param array $options
     *
     * @return mixed
     *
     * @throws Exception
     */
    public function ensureSameOrNotExistAndCreate(
        /** @noinspection PhpUnusedParameterInspection */ array $data,
        /** @noinspection PhpUnusedParameterInspection */ array $options = []
    ) {
        throw $this->createNotYetImplementedException('feature.not_available', __METHOD__);
    }
    /**
     * @param array $data
     * @param array $options
     *
     * @return array
     */
    protected function prepareCreate($data, $options = [])
    {
        $doc = $this->validateData($data, 'create', $options);

        unset($data);

        $doc = $this->refreshModel($doc, ['operation' => 'create'] + $options);

        $this->applyBusinessRules('create', $doc, $options);

        return [$doc, $this->convertToArray($doc, $options)];
    }
    /**
     * @param mixed $doc
     * @param array $array
     * @param array $options
     *
     * @return mixed
     */
    protected function completeCreate($doc, $array, $options = [])
    {
        if (property_exists($doc, 'id')) {
            if (isset($array['_id'])) {
                $doc->id = (string) $array['_id'];
            } elseif (isset($array['id'])) {
                $doc->id = (string) $array['id'];
            }
        }

        $doc = $this->cleanModel($doc, ['operation' => 'create'] + $options);

        $this->applyBusinessRules('complete_create', $doc, $options);
        $this->event('created', $doc);

        return $doc;
    }
    /**
     * @param array $array
     * @param array $options
     *
     * @return mixed
     */
    abstract protected function saveCreate(array $array, array $options = []);
    /**
     * @param array $arrays
     * @param array $options
     *
     * @return array
     */
    abstract protected function saveCreateBulk(array $arrays, array $options = []);
    /**
     * @param mixed $bulkData
     * @param array $options
     *
     * @return $this
     */
    abstract protected function checkBulkData($bulkData, $options = []);
    /**
     * @param string $msg
     * @param array  ...$params
     *
     * @return Exception
     */
    abstract protected function createNotYetImplementedException($msg, ...$params);
    /**
     * @param array  $data
     * @param string $mode
     * @param array  $options
     *
     * @return mixed
     */
    abstract protected function validateData(array $data = [], $mode = 'create', array $options = []);
    /**
     * @param mixed $model
     * @param array $options
     *
     * @return mixed
     */
    abstract protected function refreshModel($model, array $options = []);
    /**
     * @param string $operation
     * @param mixed  $model
     * @param array  $options
     *
     * @return $this
     */
    abstract protected function applyBusinessRules($operation, $model, array $options = []);
    /**
     * @param mixed $model
     * @param array $options
     *
     * @return array
     */
    abstract protected function convertToArray($model, array $options = []);
    /**
     * @param mixed $model
     * @param array $options
     *
     * @return mixed
     */
    abstract protected function cleanModel($model, array $options = []);
    /**
     * @param string $event
     * @param mixed  $data
     *
     * @return $this
     */
    abstract protected function event($event, $data = null);
}
