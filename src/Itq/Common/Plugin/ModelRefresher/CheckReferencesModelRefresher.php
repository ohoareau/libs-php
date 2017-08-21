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
class CheckReferencesModelRefresher extends Base\AbstractMetaDataAwareModelRefresher
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

        foreach ($this->getMetaDataService()->getModelReferences($doc) as $property => $reference) {
            if (null === $doc->$property) {
                continue;
            }
            if (!$this->isPopulableModelProperty($doc, $property, $options)) {
                continue;
            }
            if ('*cleared*' === $doc->$property) {
                continue;
            }
            if (isset($reference['key'])) {
                $this->checkReference($reference['key'], $doc->$property, $reference['type']);
            } else {
                $this->checkReference('id', $doc->$property, $reference['type']);
            }
        }

        return $doc;
    }
    /**
     * @param string $referenceKey
     * @param string $referenceValue
     * @param string $type
     *
     * @return $this
     *
     * @throws \Exception
     */
    protected function checkReference($referenceKey, $referenceValue, $type)
    {
        $this->getCrudService()->get($type)->checkExistBy($referenceKey, $referenceValue);

        return $this;
    }
}
