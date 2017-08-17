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
 * Formatter Service.
 *
 * @author itiQiti Dev Team <opensource@itiqiti.com>
 */
class FormatterService
{
    use Traits\ServiceTrait;
    use Traits\CallableBagTrait;
    /**
     * Register a formatter for the type (replace if exist).
     *
     * @param string   $type
     * @param callable $callable
     * @param array    $options
     *
     * @return $this
     *
     * @throws \Exception
     */
    public function register($type, $callable, array $options = [])
    {
        return $this->registerCallableByType('formatter', $type, $callable, $options);
    }
    /**
     * @param string $type
     * @param mixed  $data
     * @param array  $options
     *
     * @return mixed
     *
     * @throws \Exception
     */
    public function format($type, $data = null, array $options = [])
    {
        return $this->executeCallableByType('formatter', $type, [$data, ['format' => $type] + $options]);
    }
    /**
     * Return the formatter registered for the specified content type.
     *
     * @param string $type
     *
     * @return bool
     */
    public function has($type)
    {
        return $this->hasCallableByType('formatter', $type);
    }
}
