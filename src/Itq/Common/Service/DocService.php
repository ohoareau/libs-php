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
use Itq\Common\DocDescriptor;
use Itq\Common\Plugin\DocGeneratorInterface;

/**
 * @author itiQiti Dev Team <opensource@itiqiti.com>
 */
class DocService
{
    use Traits\ServiceTrait;
    /**
     * @param DocGeneratorInterface[] $generators
     */
    public function __construct(array $generators = [])
    {
        foreach ($generators as $target => $generator) {
            $this->addGenerator($target, $generator);
        }
    }
    /**
     * @param string                $type
     * @param DocGeneratorInterface $generator
     *
     * @return $this
     */
    public function addGenerator($type, DocGeneratorInterface $generator)
    {
        return $this->setArrayParameterKey('generators', $type, $generator);
    }
    /**
     * @return DocGeneratorInterface[]
     */
    public function getGenerators()
    {
        return $this->getArrayParameter('generators');
    }
    /**
     * @param string $type
     *
     * @return DocGeneratorInterface
     */
    public function getGenerator($type)
    {
        return $this->getArrayParameterKey('generators', $type);
    }
    /**
     * @param string $type
     * @param string $path
     * @param array  $params
     * @param array  $options
     *
     * @return mixed
     *
     * @throws \Exception
     */
    public function generate($type, $path, array $params = [], array $options = [])
    {
        return $this
            ->getGenerator($type)
            ->describe($descriptor = new DocDescriptor($type, $path, $params))
            ->generate($descriptor, $options)
        ;
    }
}
