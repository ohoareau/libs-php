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
class ComputeFingerPrintsModelRefresher extends Base\AbstractMetaDataAwareModelRefresher
{
    use Traits\ServiceAware\GeneratorServiceAwareTrait;
    /**
     * @param Service\MetaDataService  $metaDataService
     * @param Service\GeneratorService $generatorService
     */
    public function __construct(
        Service\MetaDataService $metaDataService,
        Service\GeneratorService $generatorService
    ) {
        parent::__construct($metaDataService);
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

        $fingerPrints = $this->getMetaDataService()->getModelFingerPrints($doc);

        foreach ($fingerPrints as $k => $v) {
            $values = [];

            $found = false;
            $clear = false;

            foreach ($v['of'] as $p) {
                if (!isset($doc->$p)) {
                    $values[$p] = null;
                    continue;
                } else {
                    if ('*cleared*' === $doc->$p) {
                        $clear = true;
                    } else {
                        $values[$p] = $doc->$p;
                        $found = true;
                    }
                }
            }

            unset($v['of']);

            if (true === $found) {
                $doc->$k = $this->generateValue(['type' => 'fingerprint'], count($values) > 1 ? $values : array_shift($values));
            } elseif (true === $clear) {
                $doc->$k = '*cleared*';
            }
        }

        unset($options);

        return $doc;
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
