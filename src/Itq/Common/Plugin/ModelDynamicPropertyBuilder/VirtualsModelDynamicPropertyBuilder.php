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
class VirtualsModelDynamicPropertyBuilder extends Base\AbstractModelDynamicPropertyBuilder
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
        return true === isset($m['virtuals'][$k]);
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
        return $this->computeVirtual($doc, $k, $m['virtuals'][$k], $options);
    }
    /**
     * @param mixed  $doc
     * @param string $property
     * @param array  $definition
     * @param array  $options
     *
     * @return mixed
     *
     * @throws \Exception
     */
    protected function computeVirtual($doc, $property, $definition, array $options = [])
    {
        $service = $this->getCrudByModelClass($doc);

        if (!isset($definition['params'])) {
            $definition['params'] = [$doc->id];
            if (method_exists($service, 'getExpectedTypeCount')) {
                switch ($service->getExpectedTypeCount()) {
                    case 2:
                        $definition['params'] = array_merge(
                            [isset($options['parentId']) ? $options['parentId'] : null],
                            $definition['params']
                        );
                        break;
                }
            }
        }

        if (!isset($definition['method']) || $this->isEmptyString($definition['method'])) {
            $definition['method'] = 'get'.ucfirst($property);
        }

        $method = $definition['method'];
        $params = $definition['params'];

        foreach ($params as $k => $v) {
            $matches = null;
            if (':options' === $v) {
                $params[$k] = $options;
            } elseif (0 < preg_match('/^@(.+)$/', $v, $matches)) {
                $params[$k] = isset($doc->{$matches[1]}) ? $doc->{$matches[1]} : null;
            }
        }

        if (!method_exists($service, $method)) {
            throw $this->createRequiredException("Missing method '%s' in service '%s'", $method, $this->getMetaDataService()->getModelIdForClass($doc));
        }

        $options['doc']        = $doc;
        $options['property']   = $property;
        $options['definition'] = $definition;

        $params[] = $options;

        return call_user_func_array([$service, $method], array_values($params));
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
