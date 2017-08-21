<?php

/*
 * This file is part of the COMMON package.
 *
 * (c) itiQiti SAS <opensource@itiqiti.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Itq\Common\Service;

use Itq\Common\Traits;

/**
 * DynamicUrl Service.
 *
 * @author itiQiti Dev Team <opensource@itiqiti.com>
 */
class DynamicUrlService
{
    use Traits\ServiceTrait;
    use Traits\ServiceAware\GeneratorServiceAwareTrait;
    /**
     * @param GeneratorService $generatorService
     */
    public function __construct(GeneratorService $generatorService)
    {
        $this->setGeneratorService($generatorService);
    }
    /**
     * @param mixed $doc
     * @param array $definition
     * @param array $options
     *
     * @return mixed
     */
    public function compute($doc, $definition, $options = [])
    {
        $_vars = [];

        if (!isset($definition['vars']) || !is_array($definition['vars'])) {
            $definition['vars'] = [];
        }

        foreach ($definition['vars'] as $kk => $vv) {
            if ('@' === substr($vv, 0, 1)) {
                $vv = substr($vv, 1);
                $cdoc = $doc;
                if (strpos($vv, '.')) {
                    $ps = explode('.', $vv);
                    $vv = $ps[count($ps) - 1];
                    unset($ps[count($ps) - 1]);
                    foreach ($ps as $vvv) {
                        if (isset($cdoc->$vvv)) {
                            $cdoc = $cdoc->$vvv;
                        }
                    }
                }
                $v = isset($cdoc->$vv) ? $cdoc->$vv : null;
            } else {
                $v = $vv;
            }

            if (null === $v) {
                return null;
            }

            $_vars[$kk] = $v;
        }

        $type = $definition['type'];

        foreach ($_vars as $k => $v) {
            if (false !== strpos($type, '{'.$k.'}')) {
                $type = str_replace('{'.$k.'}', (string) $v, $type);
            }
        }

        return $this->getGeneratorService()->generate('dynamicurl', ['dynamicPattern' => $type] + $_vars, $options);
    }
}
