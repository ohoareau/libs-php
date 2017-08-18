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
     * @param string $parentId
     * @param mixed  $data
     * @param array  $options
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
     * Create a new document by selecting parent from a specific field.
     *
     * @param string $parentFieldName
     * @param mixed  $parentFieldValue
     * @param mixed  $data
     * @param array  $options
     *
     * @return mixed
     */
    public function createBy($parentFieldName, $parentFieldValue, $data, $options = [])
    {
        return $this->create(
            $this->getParentIdBy($parentFieldName, $parentFieldValue),
            $data,
            $options
        );
    }
    /**
     * @param string $parentId
     * @param array  $array
     * @param array  $options
     *
     * @return mixed
     */
    protected abstract function saveCreate($parentId, array $array, array $options = []);
    /**
     * @param string $parentId
     * @param array  $arrays
     * @param array  $options
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
     * @param string $parentId
     * @param array  $data
     * @param array  $options
     *
     * @return array
     */
    protected function prepareCreate($parentId, $data, $options = [])
    {
        $doc = $this->validateData($data, 'create', $options);

        unset($data);

        $doc = $this->refreshModel($doc, ['operation' => 'create'] + $options);

        $this->applyBusinessRules($parentId, 'create', $doc, $options);

        return [$doc, $this->convertToArray($doc, $options)];
    }
    /**
     * @param string $parentId
     * @param mixed  $doc
     * @param array  $array
     * @param array  $options
     *
     * @return mixed
     */
    protected function completeCreate($parentId, $doc, $array, $options = [])
    {
        if (property_exists($doc, 'id') && isset($array['id'])) {
            $doc->id = (string) $array['id'];
        }

        $doc = $this->cleanModel($doc, ['operation' => 'create', 'parentId' => $parentId] + $options);

        $this->applyBusinessRules($parentId, 'complete_create', $doc, $options);
        $this->event($parentId, 'created', $doc);

        return $doc;
    }
}
