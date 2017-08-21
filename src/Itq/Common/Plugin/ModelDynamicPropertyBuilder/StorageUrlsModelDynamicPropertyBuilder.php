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
class StorageUrlsModelDynamicPropertyBuilder extends Base\AbstractModelDynamicPropertyBuilder
{
    use Traits\ServiceAware\CrudServiceAwareTrait;
    use Traits\ServiceAware\MetaDataServiceAwareTrait;
    use Traits\ServiceAware\GeneratorServiceAwareTrait;
    /**
     * @param Service\MetaDataService  $metaDataService
     * @param Service\CrudService      $crudService
     * @param Service\GeneratorService $generatorService
     */
    public function __construct(
        Service\MetaDataService  $metaDataService,
        Service\CrudService      $crudService,
        Service\GeneratorService $generatorService
    ) {
        $this->setMetaDataService($metaDataService);
        $this->setCrudService($crudService);
        $this->setGeneratorService($generatorService);
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
        return true === isset($m['storageUrls'][$k]);
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
        return $this->computeStorageUrl($doc, $m['storageUrls'][$k], ['requestedField' => $k] + $options);
    }
    /**
     * @param mixed $doc
     * @param array $definition
     * @param array $options
     *
     * @return mixed
     */
    protected function computeStorageUrl($doc, $definition, $options = [])
    {
        $_vars = [];

        if (!isset($definition['vars']) || !is_array($definition['vars'])) {
            $definition['vars'] = [];
        }

        $definition['vars'] += array_intersect_key($options, ['docId' => true, 'docToken' => true, 'parentId' => true, 'parentParentId' => true, 'parentToken' => true, 'parentParentToken' => true]);

        foreach ($definition['vars'] as $kk => $vv) {
            if ('@' === substr($vv, 0, 1)) {
                $vv = substr($vv, 1);
                $v = isset($doc->$vv) ? $doc->$vv : null;
            } else {
                $v = $vv;
            }

            if (null === $v) {
                return null;
            }

            $_vars[$kk] = $v;
        }

        unset($options);

        return $this->generateValue(['type' => 'storageurl'], $_vars);
    }
    /**
     * @param array $definition
     * @param mixed $data
     *
     * @return string
     *
     * @throws \Exception
     */
    protected function generateValue($definition, $data)
    {
        return $this->getGeneratorService()->generate($definition['type'], is_object($data) ? (array) $data : $data);
    }
}
