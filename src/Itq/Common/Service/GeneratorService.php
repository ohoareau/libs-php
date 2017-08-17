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
 * Generator Service.
 *
 * @author itiQiti Dev Team <opensource@itiqiti.com>
 */
class GeneratorService
{
    use Traits\ServiceTrait;
    use Traits\CallableBagTrait;
    /**
     * Register an generator for the specified name (replace if exist).
     *
     * @param string   $name
     * @param callable $callable
     * @param array    $options
     *
     * @return $this
     *
     * @throws \Exception
     */
    public function register($name, $callable, array $options = [])
    {
        return $this->registerCallableByType('generator', $name, $callable, $options);
    }
    /**
     * Return the generator registered for the specified name.
     *
     * @param string $name
     *
     * @return callable
     *
     * @throws \Exception if no generator registered for this name
     */
    public function get($name)
    {
        return $this->getCallableByType('generator', $name);
    }
    /**
     * @param string $name
     * @param mixed  $data
     * @param array  $options
     *
     * @return mixed
     *
     * @throws \Exception
     */
    public function generate($name, $data = null, array $options = [])
    {
        return $this->executeCallableByType('generator', $name, [$data, $options]);
    }
}
