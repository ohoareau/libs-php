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

/**
 * Create service trait.
 *
 * @author itiQiti Dev Team <opensource@itiqiti.com>
 */
trait CreateServiceTrait
{
    /**
     * Create a new document.
     *
     * @param mixed $parentId
     * @param mixed $data
     * @param array $options
     *
     * @return mixed
     */
    public function create($parentId, $data, $options = [])
    {
        list($doc, $array) = $this->prepareCreate($parentId, $data, $options);

        unset($data);

        if (!isset($doc->id)) {
            $doc->id = md5(sha1(md5(uniqid().rand(0, 1000).rand(0, 1000).uniqid())));
            $array['id'] = $doc->id;
        }

        $this->saveCreate($parentId, $array, $options);

        return $this->completeCreate($parentId, $doc, $array, $options);
    }
    /**
     * Create a list of documents.
     *
     * @param string $parentId
     * @param mixed  $bulkData
     * @param array  $options
     *
     * @return mixed
     */
    public function createBulk($parentId, $bulkData, $options = [])
    {
        $this->checkBulkData($bulkData, $options);

        $docs   = [];
        $arrays = [];

        foreach ($bulkData as $i => $data) {
            list($doc, $array) = $this->prepareCreate($parentId, $data, $options);
            if (!isset($doc->id)) {
                $doc->id = md5(sha1(md5(uniqid().rand(0, 1000).rand(0, 1000).uniqid())));
                $array['id'] = $doc->id;
            }
            $docs[$doc->id] = $doc;

            $this->pushCreateInBulk($arrays, $array);
            unset($bulkData[$i]);
        }

        $this->saveCreateBulk($parentId, $arrays, $options);

        foreach ($arrays as $i => $array) {
            $this->completeCreate($parentId, $docs[$array['id']], $array, $options);
        }

        unset($arrays);

        return $docs;
    }
    /**
     * @param mixed $parentId
     * @param array $array
     * @param array $options
     *
     * @return mixed
     */
    protected abstract function saveCreate($parentId, array $array, array $options = []);
    /**
     * @param mixed $parentId
     * @param array $arrays
     * @param array $options
     *
     * @return array
     */
    protected abstract function saveCreateBulk($parentId, array $arrays, array $options = []);
    /**
     * @param array $arrays
     * @param array $array
     *
     * @return mixed
     */
    protected abstract function pushCreateInBulk(&$arrays, $array);
    /**
     * Trigger the specified document event if listener are registered.
     *
     * @param string $parentId
     * @param string $event
     * @param mixed  $data
     *
     * @return $this
     */
    protected abstract function event($parentId, $event, $data = null);
    /**
     * @param string $parentId
     * @param string $operation
     * @param mixed  $model
     * @param array  $options
     *
     * @return $this
     */
    protected abstract function applyBusinessRules($parentId, $operation, $model, array $options = []);
    /**
     * Test if specified document event has registered event listeners.
     *
     * @param string $event
     *
     * @return bool
     */
    protected abstract function observed($event);
    /**
     * @param mixed $bulkData
     * @param array $options
     *
     * @return $this
     *
     * @throws \Exception
     */
    protected abstract function checkBulkData($bulkData, $options = []);
    /**
     * @param string $mode
     * @param array  $data
     * @param array  $options
     *
     * @return mixed
     */
    protected abstract function validateData(array $data = [], $mode = 'create', array $options = []);
    /**
     * @param mixed $model
     * @param array $options
     *
     * @return mixed
     */
    protected abstract function refreshModel($model, array $options = []);
    /**
     * @param mixed $model
     * @param array $options
     *
     * @return mixed
     */
    protected abstract function cleanModel($model, array $options = []);
    /**
     * Convert provided model (object) to an array.
     *
     * @param mixed $model
     * @param array $options
     *
     * @return array
     */
    protected abstract function convertToArray($model, array $options = []);
    /**
     * @param mixed $parentId
     * @param array $data
     * @param array $options
     *
     * @return array
     */
    protected function prepareCreate($parentId, $data, $options = [])
    {
        $doc = $this->validateData($data, 'create', $options);

        unset($data);

        $doc = $this->refreshModel($doc, ['operation' => 'create'] + $options);

        return [$doc, $this->applyBusinessRules($parentId, 'create', $doc, $options)->convertToArray($doc, $options)];
    }
    /**
     * @param mixed $parentId
     * @param $doc
     * @param $array
     * @param array $options
     *
     * @return mixed
     */
    protected function completeCreate($parentId, $doc, $array, $options = [])
    {
        if (property_exists($doc, 'id') && isset($array['id'])) {
            $doc->id = (string) $array['id'];
        }

        $doc = $this->cleanModel($doc, ['operation' => 'create', 'parentId' => $parentId] + $options);

        $this
            ->applyBusinessRules($parentId, 'complete_create', $doc, $options)
            ->event($parentId, 'created', $doc)
        ;

        return $doc;
    }
    /**
     * @param string $msg
     * @param array  $params
     *
     * @throws \Exception
     *
     * @return mixed
     */
    protected abstract function createRequiredException($msg, ...$params);
}
