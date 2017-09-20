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
use Itq\Common\Plugin\RequestCodecInterface;
use Symfony\Component\HttpFoundation\Request;

/**
 * @author itiQiti Dev Team <opensource@itiqiti.com>
 */
class RequestService
{
    use Traits\ServiceTrait;
    /**
     * @param string                $type
     * @param RequestCodecInterface $codec
     *
     * @return $this
     */
    public function addCodec($type, RequestCodecInterface $codec)
    {
        return $this->setArrayParameterKey('codecs', $type, $codec);
    }
    /**
     * @return RequestCodecInterface[]
     */
    public function getCodecs()
    {
        return $this->getArrayParameter('codecs');
    }
    /**
     * @return string[]
     */
    public function getCodecNames()
    {
        return $this->getArrayParameterKeys('codecs');
    }
    /**
     * @param string $type
     *
     * @return RequestCodecInterface
     */
    public function getCodec($type)
    {
        return $this->getArrayParameterKey('codecs', $type);
    }
    /**
     * @param Request $request
     * @param array   $codecs
     * @param array   $options
     *
     * @return array
     */
    public function parse(Request $request, array $codecs = [], array $options = [])
    {
        $parsed = [];

        if (!count($codecs)) {
            $codecs = $this->getCodecNames();
        }

        foreach ($codecs as $type) {
            $decoded = $this->getCodec($type)->decode($request, $options);
            if (null === $decoded) {
                continue;
            }
            $parsed[$type] = $decoded;
        }

        return $parsed;
    }
    /**
     * @param Request $request
     * @param string  $codec
     * @param array   $parsedOptionalCodecs
     * @param array   $data
     * @param array   $options
     *
     * @return mixed
     */
    public function generate(Request $request, $codec, array $parsedOptionalCodecs = [], array $data = [], array $options = [])
    {
        foreach ($parsedOptionalCodecs as $parsedOptionalCodec) {
            $this->getCodec($parsedOptionalCodec)->decode($request);
        }

        return $this->getCodec($codec)->encode($request, $data, $options);
    }
    /**
     * @param Request $request
     * @return array
     */
    public function fetchQueryCriteria(Request $request)
    {
        $v = $request->get('criteria', []);

        return is_array($v) ? $v : [];
    }
    /**
     * @param Request $request
     * @param array   $options
     *
     * @return array
     */
    public function fetchQueryFields(Request $request, array $options = [])
    {
        $options += ['key' => 'fields'];

        $v = $request->get($options['key'], []);

        if (!is_array($v) || !count($v)) {
            return [];
        }

        $fields = [];

        foreach ($v as $field) {
            if ('!' === substr($field, 0, 1)) {
                $fields[substr($field, 1)] = false;
            } else {
                $fields[$field] = true;
            }
        }

        return $fields;
    }
    /**
     * @param Request $request
     *
     * @return null|int
     */
    public function fetchQueryLimit(Request $request)
    {
        $v = $request->get('limit', null);

        return $this->isNonEmptyString($v) ? intval($v) : null;
    }
    /**
     * @param Request $request
     *
     * @return int
     */
    public function fetchQueryOffset(Request $request)
    {
        $v = intval($request->get('offset', 0));

        return 0 > $v ? 0 : $v;
    }
    /**
     * @param Request $request
     *
     * @return int
     */
    public function fetchQueryTotal(Request $request)
    {
        return null !== $request->get('total', null);
    }
    /**
     * @param Request $request
     *
     * @return array
     */
    public function fetchQuerySorts(Request $request)
    {
        $v = $request->get('sorts', []);

        if (!is_array($v) || !count($v)) {
            return [];
        }

        return array_map(
            function ($a) {
                return (int) $a;
            },
            $v
        );
    }
    /**
     * @param Request $request
     *
     * @return array
     */
    public function fetchRequestData(Request $request)
    {
        return $request->request->all();
    }
    /**
     * @param Request $request
     * @param string  $parameter
     *
     * @return mixed
     */
    public function fetchRouteParameter(Request $request, $parameter)
    {
        return $request->attributes->get($parameter);
    }
}
