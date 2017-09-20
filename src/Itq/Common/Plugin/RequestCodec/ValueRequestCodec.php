<?php

/*
 * This file is part of the COMMON package.
 *
 * (c) itiQiti SAS <opensource@itiqiti.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Itq\Common\Plugin\RequestCodec;

use Closure;
use Itq\Common\Plugin\Base\AbstractPlugin;
use Itq\Common\Plugin\RequestCodecInterface;
use Symfony\Component\HttpFoundation\Request;

/**
 * @author itiQiti Dev Team <opensource@itiqiti.com>
 */
class ValueRequestCodec extends AbstractPlugin implements RequestCodecInterface
{
    /**
     * @param null|mixed|Closure $decodeValue
     * @param null|mixed|Closure $encodeValue
     */
    public function __construct($decodeValue = null, $encodeValue = null)
    {
        $this->setDecodeValue($decodeValue);
        $this->setEncodeValue($encodeValue);
    }
    /**
     * @param null|mixed|Closure $value
     *
     * @return mixed
     */
    public function setDecodeValue($value)
    {
        return $this->setParameter('decodeValue', $value);
    }
    /**
     * @param null|mixed|Closure $value
     *
     * @return mixed
     */
    public function setEncodeValue($value)
    {
        return $this->setParameter('encodeValue', $value);
    }
    /**
     * @return mixed
     */
    public function getDecodeValue()
    {
        return $this->getParameterIfExists('decodeValue');
    }
    /**
     * @return mixed
     */
    public function getEncodeValue()
    {
        return $this->getParameterIfExists('encodeValue');
    }
    /**
     * @param Request $request
     * @param array   $options
     *
     * @return mixed
     */
    public function decode(Request $request, array $options = [])
    {
        $value = $this->getDecodeValue();

        if ($value instanceof Closure) {
            $value = $value($request, $options);
        }

        return $value;
    }
    /**
     * @param Request $request
     * @param array   $data
     * @param array   $options
     *
     * @return mixed
     */
    public function encode(Request $request, array $data = [], array $options = [])
    {
        $value = $this->getEncodeValue();

        if ($value instanceof Closure) {
            $value = $value($request, $data, $options);
        }

        return $value;
    }
}
