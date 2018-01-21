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
use Itq\Common\SdkDescriptor;
use Itq\Common\Plugin\SdkGeneratorInterface;
use Exception;

/**
 * @author itiQiti Dev Team <opensource@itiqiti.com>
 */
class SdkService
{
    use Traits\ServiceTrait;
    /**
     * @param SdkGeneratorInterface[] $generators
     *
     * @throws Exception
     */
    public function __construct(array $generators = [])
    {
        foreach ($generators as $target => $generator) {
            $this->addGenerator($target, $generator);
        }
    }
    /**
     * @param string                $target
     * @param SdkGeneratorInterface $generator
     *
     * @return $this
     *
     * @throws Exception
     */
    public function addGenerator($target, SdkGeneratorInterface $generator)
    {
        return $this->setArrayParameterKey('generators', $target, $generator);
    }
    /**
     * @return SdkGeneratorInterface[]
     *
     * @throws Exception
     */
    public function getGenerators()
    {
        return $this->getArrayParameter('generators');
    }
    /**
     * @param string $target
     *
     * @return SdkGeneratorInterface
     *
     * @throws Exception
     */
    public function getGenerator($target)
    {
        return $this->getArrayParameterKey('generators', $target);
    }
    /**
     * @param string $target
     * @param string $path
     * @param array  $params
     * @param array  $options
     *
     * @return mixed
     *
     * @throws \Exception
     */
    public function generate($target, $path, array $params = [], array $options = [])
    {
        return $this
            ->getGenerator($target)
            ->describe($descriptor = new SdkDescriptor($target, $path, $params, $options))
            ->generate($descriptor, $options)
        ;
    }
}
