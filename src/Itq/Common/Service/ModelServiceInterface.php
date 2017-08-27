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
interface ModelServiceInterface
{
    /**
     * @param mixed  $doc
     * @param string $property
     * @param array  $options
     *
     * @return mixed
     */
    public function buildDynamicUrl($doc, $property, array $options = []);
    /**
     * @param mixed $doc
     * @param array $options
     *
     * @return mixed
     */
    public function clean($doc, $options = []);
    /**
     * @param mixed $doc
     * @param array $options
     *
     * @return mixed
     *
     * @throws \Exception
     */
    public function convertObjectToArray($doc, $options = []);
    /**
     * @param array  $data
     * @param string $class
     * @param array  $options
     *
     * @return array
     */
    public function enrichUpdates($data, $class, array $options = []);
    /**
     * @param mixed $doc
     * @param array $data
     * @param array $options
     *
     * @return mixed
     */
    public function populateObject($doc, $data = [], $options = []);
    /**
     * @param mixed  $doc
     * @param mixed  $data
     * @param string $propertyName
     * @param array  $options
     *
     * @return mixed
     */
    public function populateObjectProperty($doc, $data, $propertyName, $options = []);
    /**
     * @param string $model
     * @param array  $fields
     * @param array  $options
     *
     * @return array
     */
    public function prepareFields($model, $fields, array $options = []);
    /**
     * @param mixed $doc
     * @param array $options
     *
     * @return mixed
     */
    public function refresh($doc, $options = []);
    /**
     * @param mixed $doc
     * @param array $options
     */
    public function restrict($doc, array $options = []);
}
