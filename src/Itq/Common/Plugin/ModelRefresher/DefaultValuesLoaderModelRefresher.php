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
class DefaultValuesLoaderModelRefresher extends Base\AbstractMetaDataAwareModelRefresher
{
    use Traits\ServiceAware\TenantServiceAwareTrait;
    use Traits\ServiceAware\GeneratorServiceAwareTrait;
    /**
     * @param Service\MetaDataService  $metaDataService
     * @param Service\TenantService    $tenantService
     * @param Service\GeneratorService $generatorService
     */
    public function __construct(
        Service\MetaDataService $metaDataService,
        Service\TenantService $tenantService,
        Service\GeneratorService $generatorService
    ) {
        parent::__construct($metaDataService);
        $this->setTenantService($tenantService);
        $this->setGeneratorService($generatorService);
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

        if (!isset($options['operation']) || 'create' !== $options['operation']) {
            return $doc;
        }

        $defaults = $this->getMetaDataService()->getModelDefaults($doc);

        foreach ($defaults as $k => $v) {
            if (isset($doc->$k)) {
                continue;
            }
            if (!$this->isPopulableModelProperty($doc, $k, $options)) {
                continue;
            }
            $doc->$k = $this->generateDefault($doc, $v);
        }

        return $doc;
    }
    /**
     * @param mixed $doc
     * @param mixed $v
     *
     * @return array|mixed
     */
    protected function generateDefault($doc, $v)
    {
        if (!is_array($v)) {
            return $v;
        }

        $v += ['value' => null, 'options' => []];

        if (!isset($v['generator'])) {
            if (is_string($v['value']) && '{{' === substr($v['value'], 0, 2)) {
                $matches = null;
                if (0 < preg_match_all('/\{\{([^\}]+)\}\}/', $v['value'], $matches)) {
                    foreach ($matches[0] as $i => $match) {
                        if ('.' === substr($matches[1][$i], 0, 1)) {
                            $v['value'] = isset($doc->{substr($matches[1][$i], 1)}) ? $doc->{substr($matches[1][$i], 1)} : null;
                        } elseif ('now' === $matches[1][$i]) {
                            $v['value'] = new \DateTime();
                        } elseif ('tenant' === $matches[1][$i]) {
                            $v['value'] = $this->getTenantService()->getCurrent();
                        }
                    }
                }
            }

            return $v['value'];
        }

        return $this->getGeneratorService()->generate($v['generator'], (array) $doc, $v['options']);
    }
}
