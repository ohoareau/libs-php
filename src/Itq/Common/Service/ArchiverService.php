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
 * Archiver Service.
 *
 * @author itiQiti Dev Team <opensource@itiqiti.com>
 */
class ArchiverService
{
    use Traits\ServiceTrait;
    use Traits\CallableBagTrait;
    /**
     * Register an archiver for the specified name (replace if exist).
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
        return $this->registerCallableByType('archiver', $type, $callable, $options);
    }
    /**
     * Return the archiver registered for the specified name.
     *
     * @param string $type
     *
     * @return callable
     *
     * @throws \Exception if no archiver registered for this name
     */
    public function get($type)
    {
        return $this->getCallableByType('archiver', $type);
    }
    /**
     * @param string $type
     * @param array  $data
     * @param array  $options
     *
     * @return mixed
     *
     * @throws \Exception
     */
    public function archive($type, $data, array $options = [])
    {
        return $this->executeCallableByType('archiver', $type, [$data, ['type' => $type] + $options]);
    }
}
