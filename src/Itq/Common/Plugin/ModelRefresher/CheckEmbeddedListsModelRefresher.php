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

use Exception;
use Itq\Common\Traits;
use Itq\Common\Service;
use Itq\Common\ModelInterface;

/**
 * @author itiQiti Dev Team <opensource@itiqiti.com>
 */
class CheckEmbeddedListsModelRefresher extends Base\AbstractMetaDataAwareModelRefresher
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
     *
     * @throws Exception
     */
    public function refresh($doc, array $options = [])
    {
        if (!is_object($doc)) {
            return $doc;
        }

        foreach ($this->getMetaDataService()->getModelEmbeddedLists($doc) as $property => $embeddedList) {
            if (!property_exists($doc, $property)) {
                continue;
            }
            if (!isset($doc->$property) || [] === $doc->$property) {
                if (isset($options['operation']) && 'create' === $options['operation']) {
                    $doc->$property = (object) [];
                }
                continue;
            } else {
                if (isset($options['operation'])) {
                    if ('create' === $options['operation']) {
                        throw $this->createDeniedException("Not allowed to set '%s' (embedded) on new document", $property);
                    } elseif ('update' === $options['operation']) {
                        if (!isset($embeddedList['updatable']) || true !== $embeddedList['updatable']) {
                            throw $this->createDeniedException("Not allowed to change '%s' (embedded)", $property);
                        }
                        $bulkData = $doc->$property;
                        $doc->$property = null;
                        if (!is_array($bulkData)) {
                            throw $this->createDeniedException("Not allowed to change '%s' without providing a list (embedded)", $property);
                        }
                        $subService = $this->getCrudService()->get($embeddedList['type']);
                        $subService->replaceAll($options['id'], $bulkData);
                    } else {
                        throw $this->createDeniedException("Not allowed to change '%s' (embedded) when operation is %s", $property, isset($options['operation']) ? $options['operation'] : '?');
                    }
                }
            }
        }

        return $doc;
    }
}
