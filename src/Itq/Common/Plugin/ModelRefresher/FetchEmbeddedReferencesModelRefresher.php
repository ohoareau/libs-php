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

use Itq\Common\Traits;
use Itq\Common\Service;
use Itq\Common\ModelInterface;

/**
 * @author itiQiti Dev Team <opensource@itiqiti.com>
 */
class FetchEmbeddedReferencesModelRefresher extends Base\AbstractMetaDataAwareModelRefresher
{
    use Traits\ServiceAware\CrudServiceAwareTrait;
    /**
     * @param Service\MetaDataService $metaDataService
     * @param Service\CrudService     $crudService
     */
    public function __construct(
        Service\MetaDataService $metaDataService,
        Service\CrudService $crudService
    ) {
        parent::__construct($metaDataService);
        $this->setCrudService($crudService);
    }
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

        foreach ($this->getMetaDataService()->getModelEmbeddedReferences($doc) as $property => $embeddedReference) {
            if (!isset($doc->$property)) {
                continue;
            }
            if (!$this->isPopulableModelProperty($doc, $property, $options)) {
                continue;
            }
            if ('*cleared*' === $doc->$property) {
                $doc->$property = '*cleared*';
            } elseif (isset($embeddedReference['key'])) {
                $doc->$property = $this->convertReferenceToObject($embeddedReference['key'], $doc->$property, $this->getMetaDataService()->getModelClassForId($embeddedReference['localType']), $embeddedReference['type']);
            } else {
                $doc->$property = $this->convertIdToObject($doc->$property, $this->getMetaDataService()->getModelClassForId($embeddedReference['localType']), $embeddedReference['type']);
            }
        }

        return $doc;
    }
    /**
     * @param string $referenceKey
     * @param string $referenceValue
     * @param string $class
     * @param string $type
     *
     * @return Object
     */
    protected function convertReferenceToObject($referenceKey, $referenceValue, $class, $type)
    {
        $model = $this->createModelInstance(['model' => $class]);
        $fields = array_keys(get_object_vars($model));

        return $this->getCrudService()->get($type)->getBy($referenceKey, $referenceValue, $fields, ['model' => $model]);
    }
    /**
     * @param string $id
     * @param string $class
     * @param string $type
     *
     * @return Object
     */
    protected function convertIdToObject($id, $class, $type)
    {
        $model = $this->createModelInstance(['model' => $class]);
        $fields = array_keys(get_object_vars($model));

        return $this->getCrudService()->get($type)->get($id, $fields, ['model' => $model]);
    }
}
