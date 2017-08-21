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
class BuildGeneratedsModelRefresher extends Base\AbstractMetaDataAwareModelRefresher
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
     *
     * @throws Exception
     */
    public function refresh($doc, array $options = [])
    {
        if (!is_object($doc)) {
            return $doc;
        }

        $generateds = $this->getMetaDataService()->getModelGenerateds($doc);

        foreach ($generateds as $k => $v) {
            $generate = false;
            if (isset($v['trigger'])) {
                if (isset($doc->{$v['trigger']})) {
                    $generate = true;
                }
            } else {
                if ($this->isPopulableModelProperty($doc, $k, $options)) {
                    $generate = true;
                }
            }
            if (true === $generate) {
                $value = $this->generateValue($v, $doc);

                if (isset($v['encode'])) {
                    switch ($v['encode']) {
                        case 'base64':
                            $value = base64_encode($value);
                            break;
                        default:
                            throw $this->createUnexpectedException("Unsupported encode option '%s'", $v['encode']);
                    }
                }

                $doc->$k = $value;
            }
        }

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
