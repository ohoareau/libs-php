<?php

/*
 * This file is part of the COMMON package.
 *
 * (c) itiQiti SAS <opensource@itiqiti.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Itq\Common\Service\Model;

use Closure;
use Exception;
use Itq\Common\Traits;
use Itq\Common\Service;

/**
 * @author itiQiti Dev Team <opensource@itiqiti.com>
 */
class ModelPropertyLinearizerService extends Base\AbstractModelPropertyLinearizerService
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
     * @param object $doc
     * @param array  $options
     *
     * @return array|object
     *
     * @throws Exception
     */
    public function linearize($doc, $options = [])
    {
        if (!is_object($doc)) {
            throw $this->createMalformedException('Not a valid object');
        }

        $options         += ['removeNulls' => true];
        $removeNulls      = true === $options['removeNulls'];
        $meta             = $this->getMetaDataService()->getModel($doc);
        $data             = get_object_vars($doc);
        $globalObjectCast = 'stdClass' === get_class($doc);
        $that             = $this;
        $objectLinearizer = function ($doc, $options) use ($that) {
            return $that->linearize($doc, $options);
        };

        foreach ($data as $k => $v) {
            if ($removeNulls && null === $v) {
                unset($data[$k]);
                continue;
            }
            if (is_string($v) && false !== strpos($v, '*cleared*')) {
                $v       = null;
                $doc->$k = $v;
            }
            $this->linearizeProperty($data, $k, $v, $meta, $objectLinearizer, $options);
        }

        return (true === $globalObjectCast) ? ((object) $data) : $data;
    }
    /**
     * @param array   $data
     * @param string  $k
     * @param mixed   $v
     * @param array   $meta
     * @param Closure $objectLinearizer
     * @param array   $options
     *
     * @return void
     */
    public function linearizeProperty(array &$data, $k, $v, array &$meta, Closure $objectLinearizer, array $options = [])
    {
        foreach ($this->getModelPropertyLinearizers() as $propertyLinearizer) {
            if (!$propertyLinearizer->supports($data, $k, $v, $meta, $options)) {
                continue;
            }
            $propertyLinearizer->linearize($data, $k, $v, $meta, $objectLinearizer, $options);
            break;
        }
    }
}
