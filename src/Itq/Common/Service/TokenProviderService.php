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
use Itq\Common\Exception;

/**
 * @author itiQiti Dev Team <opensource@itiqiti.com>
 */
class TokenProviderService
{
    use Traits\ServiceTrait;
    /**
     * @param mixed  $generator
     * @param string $type
     * @param string $method
     * @param array  $data
     *
     * @return $this
     */
    public function addGenerator($generator, $type = 'default', $method = 'create', array $data = [])
    {
        return $this->setArrayParameterKey('generators', $type, ['method' => $method, 'data' => $data, 'generator' => $generator]);
    }
    /**
     * @param string $type
     * @param array  $payload
     *
     * @return string
     *
     * @throws \Exception
     */
    public function generate($type, array $payload = [])
    {
        if (!$this->hasArrayParameterKey('generators', $type)) {
            throw new Exception\UnsupportedTokenGeneratorTypeException($type);
        }

        $infos     = $this->getArrayParameterKey('generators', $type);
        $generator = $infos['generator'];
        $method    = $infos['method'];
        $data      = $infos['data'];

        if (!method_exists($generator, $method)) {
            throw $this->createNotFoundException(
                "Unable to generate token from generator '%s' (method: %s)",
                get_class($generator),
                $method
            );
        }

        $data['data']  = (!isset($data['data']) || !is_array($data['data'])) ? [] : $data['data'];
        $data['data'] += $payload;

        return $generator->{$method}($data);
    }
}
