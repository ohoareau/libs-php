<?php

/*
 * This file is part of the COMMON package.
 *
 * (c) itiQiti SAS <opensource@itiqiti.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Itq\Common\Plugin\ModelRefresher;

use Itq\Common\ModelInterface;

/**
 * @author itiQiti Dev Team <opensource@itiqiti.com>
 */
class CheckEmbeddedsModelRefresher extends Base\AbstractMetaDataAwareModelRefresher
{
    /**
     * @param ModelInterface $doc
     * @param array          $options
     *
     * @return ModelInterface
     */
    public function refresh($doc, array $options = [])
    {
        if (!is_object($doc)) {
            return $doc;
        }

        foreach ($this->getMetaDataService()->getModelEmbeddeds($doc) as $property => $embedded) {
            if (!$this->isPopulableModelProperty($doc, $property, $options)) {
                continue;
            }
            $type = $this->getMetaDataService()->getModelPropertyType($doc, $property);
            $doc->$property = $this->convertMixedToObject($doc->$property, isset($embedded['class']) ? $embedded['class'] : ($type ? $type['type'] : null), $embedded['type']);
        }

        return $doc;
    }
    /**
     * @param string $data
     * @param string $class
     * @param string $type
     *
     * @return mixed
     *
     * @throws \Exception
     */
    protected function convertMixedToObject($data, $class, $type)
    {
        unset($type);

        $model = $this->createModelInstance(['model' => $class]);
        $fields = array_keys(get_object_vars($model));

        if (is_object($data)) {
            if (get_class($data) !== $class && !is_subclass_of($data, $class)) {
                $data = (array) $data;
            } else {
                return $data;
            }
        }
        if (null === $data) {
            return null;
        }

        if (!is_array($data)) {
            throw $this->createMalformedException("Array expected to be able to convert to %s", $class);
        }

        if (!count($data)) {
            return null;
        }

        foreach ($fields as $field) {
            if (!isset($data[$field])) {
                continue;
            }
            $model->$field = $data[$field];
        }

        return $model;
    }
}
