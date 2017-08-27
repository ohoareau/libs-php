<?php

/*
 * This file is part of the COMMON package.
 *
 * (c) itiQiti SAS <opensource@itiqiti.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Itq\Common\Plugin\ModelCleaner;

use Exception;
use Itq\Common\Traits;
use Itq\Common\Service;
use Itq\Common\ModelInterface;

/**
 * @author itiQiti Dev Team <opensource@itiqiti.com>
 */
class RefreshEmbeddedReferenceLinksModelCleaner extends Base\AbstractMetaDataAwareModelCleaner
{
    use Traits\ServiceAware\CrudServiceAwareTrait;
    use Traits\ServiceAware\Model\ModelPropertyLinearizerServiceAwareTrait;
    /**
     * @param Service\MetaDataService                               $metaDataService
     * @param Service\CrudService                                   $crudService
     * @param Service\Model\ModelPropertyLinearizerServiceInterface $modelPropertyLinearizerService
     */
    public function __construct(
        Service\MetaDataService $metaDataService,
        Service\CrudService     $crudService,
        Service\Model\ModelPropertyLinearizerServiceInterface $modelPropertyLinearizerService
    ) {
        parent::__construct($metaDataService);
        $this->setCrudService($crudService);
        $this->setModelPropertyLinearizerService($modelPropertyLinearizerService);
    }
    /**
     * @param ModelInterface $doc
     * @param array          $options
     *
     * @return void
     *
     * @throws Exception
     */
    public function clean($doc, array $options = [])
    {
        if (!isset($options['operation']) || 'update' !== $options['operation']) {
            return;
        }

        $embeddedReferenceLinks = $this->getMetaDataService()->getModelEmbeddedReferenceLinks($doc, $options);

        if (!count($embeddedReferenceLinks)) {
            return;
        }

        $fields = [];

        foreach ($embeddedReferenceLinks as $linkName => $link) {
            $fields += $link['fields'];
        }

        $service = $this->getCrudByModelClass($doc);

        switch ($service->getExpectedTypeCount()) {
            case 1:
                $fullDoc = $service->get($doc->getId(), $fields);
                break;
            case 2:
                $fullDoc = $service->get($options['parentId'], $doc->getId(), $fields);
                break;
            default:
                throw $this->createUnexpectedException(
                    "Unsupported number of expected type '%d' for embedded reference links",
                    $service->getExpectedTypeCount()
                );
        }

        foreach ($embeddedReferenceLinks as $linkName => $link) {
            $joinDocClass = $link['joinClass'];
            $joinDoc = new $joinDocClass();
            foreach (array_keys(get_object_vars($joinDoc)) as $field) {
                $joinDoc->$field = isset($fullDoc->$field) ? $fullDoc->$field : null;
            }
            $joinDocArray = $this->getModelPropertyLinearizerService()->linearize($joinDoc, $options);
            $owningSideService = $this->getCrudService()->get($link['owningSideType']);
            switch ($owningSideService->getExpectedTypeCount()) {
                case 1:
                    $selectCriteria = [$link['owningSideField'].'.id' => $fullDoc->id];
                    $set = [$link['owningSideField'] => $joinDocArray];
                    break;
                default:
                    return;
            }
            $owningSideService->getRepository()->alter($selectCriteria, ['$set' => $set], ['multiple' => true]);
        }
    }
    /**
     * @param string $class
     *
     * @return mixed
     */
    protected function getCrudByModelClass($class)
    {
        return $this->getCrudService()->get($this->getMetaDataService()->getModel($class)['id']);
    }
}
