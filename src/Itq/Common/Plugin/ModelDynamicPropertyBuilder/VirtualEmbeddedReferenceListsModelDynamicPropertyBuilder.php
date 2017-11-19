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
class VirtualEmbeddedReferenceListsModelDynamicPropertyBuilder extends Base\AbstractModelDynamicPropertyBuilder
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
        return true === isset($m['virtualEmbeddedReferenceLists'][$k]);
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
        $virtualEmbeddedReferenceList = $m['virtualEmbeddedReferenceLists'][$k];

        if (isset($virtualEmbeddedReferenceList['criteria'])) {
            if (!is_array($virtualEmbeddedReferenceList['criteria'])) {
                $virtualEmbeddedReferenceList['criteria'] = [];
            }
            $criteria = [];
            foreach ($virtualEmbeddedReferenceList['criteria'] as $kkk => $vvv) {
                if ('@' === substr($vvv, 0, 1)) {
                    $pName = substr($vvv, 1);
                    $tokens = explode('.', $pName);
                    $lastToken = array_pop($tokens);
                    foreach ($tokens as $ppName) {
                        $doc = $doc->$ppName;
                    }
                    $vvv = $doc->$lastToken;
                }
                $criteria[$kkk] = $vvv;
            }
        } else {
            $criteria = [$virtualEmbeddedReferenceList['key'] => $doc->{$virtualEmbeddedReferenceList['localKey']}];
        }

        $sorts  = [];
        $limit  = null;
        $offset = null;

        if (isset($virtualEmbeddedReferenceList['sorts']) && is_array($virtualEmbeddedReferenceList['sorts'])) {
            $sorts = $virtualEmbeddedReferenceList['sorts'];
        }
        if (isset($virtualEmbeddedReferenceList['limit']) && is_numeric($virtualEmbeddedReferenceList['limit']) && 0 < $virtualEmbeddedReferenceList['limit']) {
            $limit = (int) $virtualEmbeddedReferenceList['limit'];
        }
        if (isset($virtualEmbeddedReferenceList['offset']) && is_numeric($virtualEmbeddedReferenceList['offset']) && 0 < $virtualEmbeddedReferenceList['offset']) {
            $offset = (int) $virtualEmbeddedReferenceList['offset'];
        }

        return $this->getCrudService()->get($virtualEmbeddedReferenceList['type'])
            ->find(
                $criteria,
                $virtualEmbeddedReferenceList['fields'],
                $limit,
                $offset,
                $sorts,
                ['model' => $this->getMetaDataService()->getModelClassForId($virtualEmbeddedReferenceList['itemType'])]
            )
        ;
    }
}
