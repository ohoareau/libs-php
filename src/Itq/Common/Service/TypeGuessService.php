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
use Itq\Common\Plugin\TypeGuessBuilderInterface;

use Symfony\Component\Form\Guess\TypeGuess;

/**
 * @author itiQiti Dev Team <opensource@itiqiti.com>
 */
class TypeGuessService
{
    use Traits\ServiceTrait;
    /**
     * @param string                    $name
     * @param TypeGuessBuilderInterface $type
     *
     * @return $this
     */
    public function add($name, TypeGuessBuilderInterface $type)
    {
        return $this->setArrayParameterKey('typeGuessBuilders', $name, $type);
    }
    /**
     * @param string $name
     *
     * @return bool
     */
    public function has($name)
    {
        return $this->hasArrayParameterKey('typeGuessBuilders', $name);
    }
    /**
     * @param string $name
     *
     * @return TypeGuessBuilderInterface
     */
    public function get($name)
    {
        return $this->getArrayParameterKey('typeGuessBuilders', $name);
    }
    /**
     * @param string $type
     * @param array  $definition
     * @param array  $options
     *
     * @return TypeGuess
     */
    public function create($type, array $definition, array $options = [])
    {
        if ($this->has($type)) {
            return $this->get($type)->build($definition, $options);
        }

        return $this->get('unknown')->build([], $options);
    }
}
