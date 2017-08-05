<?php

/*
 * This file is part of the COMMON package.
 *
 * (c) itiQiti SAS <cto@itiqiti.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Itq\Common\Service;

use Itq\Common\Traits;
use Itq\Common\Plugin\CheckUpInterface;

/**
 * Data Service.
 *
 * @author itiQiti Dev Team <opensource@itiqiti.com>
 */
class DataService
{
    use Traits\ServiceTrait;
    /**
     * @param CheckUpInterface[] $checkUps
     */
    public function __construct(array $checkUps = [])
    {
        foreach ($checkUps as $name => $checkUp) {
            $this->addCheckUp($name, $checkUp);
        }
    }
    /**
     * @param string           $name
     * @param CheckUpInterface $checkUp
     *
     * @return $this
     */
    public function addCheckUp($name, CheckUpInterface $checkUp)
    {
        return $this->setArrayParameterKey('checkUps', $name, $checkUp);
    }
    /**
     * @return CheckUpInterface[]
     */
    public function getCheckUps()
    {
        return $this->getArrayParameter('checkUps');
    }
    /**
     * @param string $name
     *
     * @return CheckUpInterface
     */
    public function getCheckUp($name)
    {
        return $this->getArrayParameterKey('checkUps', $name);
    }
    /**
     * @param array $types
     * @param array $vars
     * @param array $options
     *
     * @return $this
     */
    public function checkUp(array $types, array $vars = [], array $options = [])
    {
        if (in_array('all', $types)) {
            $types = array_keys($this->getCheckUps());
        }

        foreach ($types as $type) {
            $this->getCheckUp($type)->checkUp($vars, $options);
        }

        return $this;
    }
}
