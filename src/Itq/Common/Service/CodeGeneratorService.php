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
use Zend\Code\Generator\FileGenerator;
use Zend\Code\Generator\ClassGenerator;
use Zend\Code\Generator\MethodGenerator;
use Zend\Code\Generator\PropertyGenerator;
use Zend\Code\Generator\DocBlockGenerator;
use Zend\Code\Generator\PropertyValueGenerator;
use Zend\Code\Generator\DocBlock\Tag\GenericTag;

/**
 * @author itiQiti Dev Team <opensource@itiqiti.com>
 */
class CodeGeneratorService
{
    use Traits\ServiceTrait;
    use Traits\CallableBagTrait;
    /**
     * @param string $name
     * @param array  $definition
     *
     * @return FileGenerator
     */
    public function createClassFile($name, $definition = [])
    {
        list($namespace) = $this->explodeClassNamespace($name);

        $zFile = $this->createFile(['namespace' => $namespace] + $definition);
        $zFile->setClass($this->createClass($name, ['zFile' => $zFile, 'namespace' => false] + $definition));

        return $zFile;
    }
    /**
     * @param array $definition
     *
     * @return FileGenerator
     */
    public function createFile($definition = [])
    {
        $zFile = new FileGenerator();

        if (isset($definition['uses'])) {
            $zFile->setUses($definition['uses']);
        }

        if (isset($definition['namespace'])) {
            $zFile->setNamespace($definition['namespace']);
        }

        return $zFile;
    }
    /**
     * @param string $name
     * @param array  $definition
     *
     * @return ClassGenerator
     */
    public function createClass($name, $definition = [])
    {
        $definition += ['methods' => [], 'uses' => [], 'properties' => []];

        $zMethods    = [];
        $zProperties = [];

        foreach ($definition['methods'] as $methodName => $method) {
            $zMethods[] = $this->createMethod($methodName, $method + $definition);
        }

        foreach ($definition['properties'] as $propertyName => $property) {
            $params = isset($property['params']) ? $property['params'] : [];
            unset($property['params']);
            $zProperties[] = $this->createProperty($propertyName, $property + $params + $definition);
        }

        list($namespace, $baseName) = $this->explodeClassNamespace($name);

        if (isset($definition['namespace']) && false === $definition['namespace']) {
            $namespace = null;
        }

        $parent = null;

        if (isset($definition['parent'])) {
            $parent = $definition['parent'];
        }

        $zClass = new ClassGenerator($baseName, $namespace, null, $parent, null, $zProperties, $zMethods);

        if (isset($definition['traits'])) {
            $zClass->addTraits($definition['traits']);
        }

        return $zClass;
    }
    /**
     * @param string $name
     * @param array  $definition
     *
     * @return MethodGenerator
     *
     * @throws \Exception
     */
    public function createMethod($name, $definition = [])
    {
        $definition += ['type' => null, 'params' => []];

        $visibility = MethodGenerator::FLAG_PUBLIC;

        if (isset($definition['visibility'])) {
            switch ($definition['visibility']) {
                case 'private':
                    $visibility = MethodGenerator::FLAG_PRIVATE;
                    break;
                case 'protected':
                    $visibility = MethodGenerator::FLAG_PROTECTED;
                    break;
            }
        }

        $zMethod = new MethodGenerator($name, [], $visibility);

        if (null !== $definition['type']) {
            $type = $definition['type'];
            unset($definition['type']);
            if (isset($definition['options']) && isset($definition['params']['options']) && is_array($definition['params']['options']) && [] === $definition['options']) {
                unset($definition['options']);
            }
            $definition += $definition['params'];
            unset($definition['params']);
            $this->executeCallableByType('methodType', $type, [$zMethod, $definition]);
        }

        return $zMethod;
    }
    /**
     * @param string   $name
     * @param callable $callable
     *
     * @return $this
     */
    public function registerMethodType($name, $callable)
    {
        return $this->registerCallableByType('methodType', $name, $callable);
    }
    /**
     * @param string $name
     * @param array  $definition
     *
     * @return PropertyGenerator
     *
     * @throws \Exception
     */
    public function createProperty($name, $definition = [])
    {
        $definition += ['type' => 'basic', 'visibility' => 'public'];

        switch ($definition['visibility']) {
            case 'private':
                $visibility = PropertyGenerator::FLAG_PRIVATE;
                break;
            case 'protected':
                $visibility = PropertyGenerator::FLAG_PROTECTED;
                break;
            case 'public':
                $visibility = PropertyGenerator::FLAG_PUBLIC;
                break;
            default:
                $visibility = PropertyGenerator::FLAG_PUBLIC;
                break;
        }

        $flags = $visibility;

        if (isset($definition['static']) && true === $definition['static']) {
            $flags |= PropertyGenerator::FLAG_STATIC;
        }

        $zProperty = new PropertyGenerator($name, isset($definition['default']) ? $definition['default'] : new PropertyValueGenerator(), $flags);

        $buildTypeProperty = 'build'.ucfirst(str_replace(' ', '', ucwords(str_replace('.', ' ', $definition['type'])))).'Property';

        if (!method_exists($this, $buildTypeProperty)) {
            $buildTypeProperty = 'buildBasicProperty';
        }

        unset($definition['type']);

        $this->$buildTypeProperty($zProperty, $definition);

        if (isset($definition['cast'])) {
            if (null === $zProperty->getDocBlock()) {
                $zProperty->setDocBlock(new DocBlockGenerator());
            }
            $zProperty->getDocBlock()->setTag(new GenericTag('var', is_array($definition['cast']) ? join('|', $definition['cast']) : $definition['cast'], $name));
        }

        return $zProperty;
    }
    /**
     * @param PropertyGenerator $zMethod
     * @param array           $definition
     */
    protected function buildBasicProperty(PropertyGenerator $zMethod, $definition = [])
    {
    }
    /**
     * @param string $name
     *
     * @return array
     */
    protected function explodeClassNamespace($name)
    {
        $pos = strrpos($name, '\\');

        if (false === $pos) {
            return [null, $name];
        }

        return [substr($name, 0, $pos), substr($name, $pos + 1)];
    }
}
