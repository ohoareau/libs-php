<?php

/*
 * This file is part of the COMMON package.
 *
 * (c) itiQiti SAS <opensource@itiqiti.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Itq\Common\Plugin\ModelDynamicPropertyBuilder;

use Itq\Common\Traits;
use Itq\Common\Service;
use Itq\Common\ModelInterface;

/**
 * @author itiQiti Dev Team <opensource@itiqiti.com>
 */
class VirtualEmbeddedReferencesModelDynamicPropertyBuilder extends Base\AbstractModelDynamicPropertyBuilder
{
    use Traits\ServiceAware\CrudServiceAwareTrait;
    use Traits\ServiceAware\MetaDataServiceAwareTrait;
    /**
     * @param Service\MetaDataService $metaDataService
     * @param Service\CrudService     $crudService
     */
    public function __construct(Service\MetaDataService $metaDataService, Service\CrudService $crudService)
    {
        $this->setMetaDataService($metaDataService);
        $this->setCrudService($crudService);
    }
    /**
     * @param ModelInterface $doc
     * @param string         $k
     * @param array          $m
     *
     * @return bool
     */
    public function supports($doc, $k, array &$m)
    {
        return true === isset($m['virtualEmbeddedReferences'][$k]);
    }
    /**
     * @param ModelInterface $doc
     * @param string         $k
     * @param array          $m
     * @param array          $options
     *
     * @return mixed
     */
    public function build($doc, $k, array &$m, array $options = [])
    {
        $virtualEmbeddedReference = $m['virtualEmbeddedReferences'][$k];
        if (isset($virtualEmbeddedReference['criteria'])) {
            if (!is_array($virtualEmbeddedReference['criteria'])) {
                $virtualEmbeddedReference['criteria'] = [];
            }
            $criteria = [];
            foreach ($virtualEmbeddedReference['criteria'] as $kkk => $vvv) {
                if ('@' === substr($vvv, 0, 1)) {
                    $vvv = $doc->{substr($vvv, 1)};
                }
                $criteria[$kkk] = $vvv;
            }
        } else {
            $criteria = [$virtualEmbeddedReference['key'] => $doc->{$virtualEmbeddedReference['localKey']}];
        }

        return $this->getCrudService()->get($virtualEmbeddedReference['type'])
            ->findOne(
                $criteria,
                $virtualEmbeddedReference['fields'],
                0,
                [],
                ['model' => $this->getMetaDataService()->getModelClassForId($virtualEmbeddedReference['itemType'])]
            )
        ;
    }
}
