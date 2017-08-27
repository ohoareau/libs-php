<?php

/*
 * This file is part of the COMMON package.
 *
 * (c) itiQiti SAS <opensource@itiqiti.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Itq\Common\Service\Model;

use Itq\Common\Traits;
use Itq\Common\Service;

/**
 * @author itiQiti Dev Team <opensource@itiqiti.com>
 */
class ModelObjectPopulatorService extends Base\AbstractModelObjectPopulatorService
{
    use Traits\ServiceAware\StorageServiceAwareTrait;
    use Traits\ServiceAware\MetaDataServiceAwareTrait;
    use Traits\ServiceAware\Model\ModelPropertyMutatorServiceAwareTrait;
    use Traits\ServiceAware\Model\ModelDynamicPropertyBuilderServiceAwareTrait;
    /**
     * @param Service\MetaDataService                     $metaDataService
     * @param Service\StorageService                      $storageService
     * @param ModelPropertyMutatorServiceInterface        $modelPropertyMutatorService
     * @param ModelDynamicPropertyBuilderServiceInterface $modelDynamicPropertyBuilderService
     */
    public function __construct(
        Service\MetaDataService $metaDataService,
        Service\StorageService $storageService,
        ModelPropertyMutatorServiceInterface $modelPropertyMutatorService,
        ModelDynamicPropertyBuilderServiceInterface $modelDynamicPropertyBuilderService
    ) {
        $this->setMetaDataService($metaDataService);
        $this->setStorageService($storageService);
        $this->setModelPropertyMutatorService($modelPropertyMutatorService);
        $this->setModelDynamicPropertyBuilderService($modelDynamicPropertyBuilderService);
    }
    /**
     * @param mixed $doc
     * @param array $data
     * @param array $options
     *
     * @return mixed
     */
    public function populateObject($doc, $data = [], $options = [])
    {
        if (isset($data['_id']) && !isset($data['id'])) {
            $data['id'] = (string) $data['_id'];
            unset($data['_id']);
        } elseif (isset($data['id'])) {
            $data['id'] = (string) $data['id'];
        }

        $ctx             = (object) ['models' => []];
        $this->getModelPropertyMutatorService()->mutate($doc, $data, $ctx, $this, $options);
        $this->getModelDynamicPropertyBuilderService()->build(
            $doc,
            isset($options['requestedFields']) ? $options['requestedFields'] : [],
            $ctx,
            $options
        );
        $this->getStorageService()->populate($doc, $this->getMetaDataService()->getModelStorages($doc), $options);

        return $doc;
    }
    /**
     * @param mixed  $doc
     * @param mixed  $data
     * @param string $propertyName
     * @param array  $options
     *
     * @return mixed
     *
     * @throws \Exception
     */
    public function populateObjectProperty($doc, $data, $propertyName, $options = [])
    {
        if (!property_exists($doc, $propertyName)) {
            throw $this->createRequiredException("Property '%s' does not exist on %s", $propertyName, get_class($doc));
        }

        $doc->$propertyName = $data;

        $this->getStorageService()->populate($doc, $this->getMetaDataService()->getModelStorages($doc), $options);

        return $doc->$propertyName;
    }
}
