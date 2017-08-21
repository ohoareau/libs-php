<?php

/*
 * This file is part of the COMMON package.
 *
 * (c) itiQiti SAS <opensource@itiqiti.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Itq\Common\Plugin\ModelPropertyMutator;

use Closure;
use Itq\Common\Traits;
use Itq\Common\Service;
use Itq\Common\ModelInterface;

/**
 * @author itiQiti Dev Team <opensource@itiqiti.com>
 */
class EmbeddedsModelPropertyMutator extends Base\AbstractModelPropertyMutator
{
    use Traits\ServiceAware\MetaDataServiceAwareTrait;
    /**
     * @param Service\MetaDataService $metaDataService
     */
    public function __construct(Service\MetaDataService $metaDataService)
    {
        $this->setMetaDataService($metaDataService);
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
        return true === isset($m['embeddeds'][$k]);
    }
    /**
     * @param ModelInterface $doc
     * @param string         $k
     * @param mixed          $v
     * @param array          $m
     * @param array          $data
     * @param Closure        $objectMutator
     * @param array          $options
     *
     * @return mixed
     */
    public function mutate($doc, $k, $v, array &$m, array &$data, Closure $objectMutator, array $options = [])
    {
        return $objectMutator(
            $v,
            $this->createModelInstance(
                [
                    'model' => $this->getMetaDataService()->getModelClassForId($m['embeddeds'][$k]['type']),
                ]
            ),
            $options
        );
    }
    /**
     * @param array $options
     *
     * @return object
     */
    protected function createModelInstance(array $options)
    {
        $class = $options['model'];

        return new $class();
    }
}
