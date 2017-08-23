<?php

/*
 * This file is part of the COMMON package.
 *
 * (c) itiQiti SAS <opensource@itiqiti.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Itq\Common\Plugin\CodeGenerator\Sdk\Crud;

use Zend\Code\Generator\MethodGenerator;
use Zend\Code\Generator\DocBlockGenerator;
use Zend\Code\Generator\ParameterGenerator;
use Zend\Code\Generator\DocBlock\Tag\ParamTag;
use Zend\Code\Generator\DocBlock\Tag\ReturnTag;
use Zend\Code\Generator\DocBlock\Tag\ThrowsTag;
use /** @noinspection PhpUnusedAliasInspection */ Itq\Common\Annotation;

/**
 * @author itiQiti Dev Team <opensource@itiqiti.com>
 */
class DeleteCrudSdkCodeGenerator extends Base\AbstractCrudSdkCodeGenerator
{
    /**
     * @param MethodGenerator $zMethod
     * @param array           $definition
     *
     * @Annotation\CodeGeneratorMethodType("crud.delete")
     */
    public function generateCrudDeleteMethod(MethodGenerator $zMethod, $definition = [])
    {
        $definition['route'] = str_replace('{id}', '%s', $definition['route']);

        $zMethod->setDocBlock(new DocBlockGenerator(
            sprintf('Delete the specified %s', $definition['type']),
            null,
            [
                new ParamTag('id', ['mixed'], sprintf('ID of the %s', $definition['type'])),
                new ParamTag('options', ['array'], 'Options'),
                new ReturnTag(['array']),
                new ThrowsTag(['\\Exception'], 'if an error occured'),
            ]
        ));
        $zMethod->setParameters([
            new ParameterGenerator('id', 'string'),
            new ParameterGenerator('options', 'array', []),
        ]);
        $zMethod->setBody(
            sprintf('return $this->getSdk()->delete(sprintf(\'%s\', $id), $options + $this->options);', $definition['route'])
        );
    }
    /**
     * @param MethodGenerator $zMethod
     * @param array           $definition
     *
     * @Annotation\CodeGeneratorMethodType("crud.deleteBy2Keys")
     */
    public function generateCrudDeleteBy2KeysMethod(MethodGenerator $zMethod, $definition = [])
    {
        $definition['route'] = str_replace(['{'.$definition['key'].'}', '{'.$definition['key2'].'}'], '%s', $definition['route']);

        $zMethod->setDocBlock(new DocBlockGenerator(
            sprintf('Delete the specified %s by %s and %s', $definition['type'], $definition['key'], $definition['key2']),
            null,
            [
                new ParamTag($definition['key'], ['string'], sprintf('%s of the %s', $definition['key'], $definition['type'])),
                new ParamTag($definition['key2'], ['string'], sprintf('%s of the %s', $definition['key2'], $definition['type'])),
                new ParamTag('options', ['array'], 'Options'),
                new ReturnTag(['array']),
                new ThrowsTag(['\\Exception'], 'if an error occured'),
            ]
        ));
        $zMethod->setParameters([
            new ParameterGenerator($definition['key'], 'string'),
            new ParameterGenerator($definition['key2'], 'string'),
            new ParameterGenerator('options', 'array', []),
        ]);
        $zMethod->setBody(
            sprintf('return $this->getSdk()->delete(sprintf(\'%s\', $%s, $%s), $options + $this->options);', $definition['route'], $definition['key'], $definition['key2'])
        );
    }
    /**
     * @param MethodGenerator $zMethod
     * @param array           $definition
     *
     * @Annotation\CodeGeneratorMethodType("crud.purge")
     */
    public function generateCrudPurgeMethod(MethodGenerator $zMethod, $definition = [])
    {
        $zMethod->setDocBlock(new DocBlockGenerator(
            sprintf('Purge all %ss', $definition['type']),
            null,
            [
                new ParamTag('criteria', ['array'], 'Optional criteria to filter deleteds'),
                new ParamTag('options', ['array'], 'Options'),
                new ReturnTag(['array']),
                new ThrowsTag(['\\Exception'], 'if an error occured'),
            ]
        ));
        $zMethod->setParameters([
            new ParameterGenerator('criteria', 'array', []),
            new ParameterGenerator('options', 'array', []),
        ]);
        $zMethod->setBody(
            sprintf('return $this->getSdk()->purge(\'%s\', $criteria, $options + $this->options);', $definition['route'])
        );
    }
    /**
     * @param MethodGenerator $zMethod
     * @param array           $definition
     *
     * @Annotation\CodeGeneratorMethodType("crud.clearProperty")
     */
    public function generateCrudClearPropertyMethod(MethodGenerator $zMethod, $definition = [])
    {
        $definition['route'] = str_replace(['{id}', '{property}'], ['%s', $definition['property']], $definition['route']);

        $zMethod->setDocBlock(new DocBlockGenerator(
            sprintf('Clear the specified %s %s', $definition['type'], $definition['property']),
            null,
            [
                new ParamTag('id', ['mixed'], sprintf('ID of the %s', $definition['type'])),
                new ParamTag('options', ['array'], 'Options'),
                new ReturnTag(['mixed']),
                new ThrowsTag(['\\Exception'], 'if an error occured'),
            ]
        ));
        $zMethod->setParameters([
            new ParameterGenerator('id', 'string'),
            new ParameterGenerator('options', 'array', []),
        ]);

        $returnBody = sprintf('$this->getSdk()->delete(sprintf(\'%s\', $id), %s, $options + $this->options)', $definition['route'], str_replace("\n", '', var_export(isset($definition['data']) ? $definition['data'] : [], true)));

        $zMethod->setBody(
            'return '.$returnBody.';'
        );
    }
    /**
     * @param MethodGenerator $zMethod
     * @param array           $definition
     *
     * @Annotation\CodeGeneratorMethodType("crud.clearPropertyBy2Keys")
     */
    public function generateCrudClearPropertyBy2KeysMethod(MethodGenerator $zMethod, $definition = [])
    {
        $definition['route'] = str_replace(['{'.$definition['key'].'}', '{'.$definition['key2'].'}', '{property}'], ['%s', '%s', $definition['property']], $definition['route']);

        $zMethod->setDocBlock(new DocBlockGenerator(
            sprintf('Clear the specified %s %s by its %s and its %s', $definition['type'], $definition['property'], $definition['key'], $definition['key2']),
            null,
            [
                new ParamTag($definition['key'], ['string'], sprintf('%s of the %s', $definition['key'], $definition['type'])),
                new ParamTag($definition['key2'], ['string'], sprintf('%s of the %s', $definition['key2'], $definition['type'])),
                new ParamTag('options', ['array'], 'Options'),
                new ReturnTag(['mixed']),
                new ThrowsTag(['\\Exception'], 'if an error occured'),
            ]
        ));
        $zMethod->setParameters([
            new ParameterGenerator($definition['key'], 'string'),
            new ParameterGenerator($definition['key2'], 'string'),
            new ParameterGenerator('options', 'array', []),
        ]);

        $returnBody = sprintf('$this->getSdk()->delete(sprintf(\'%s\', $%s, $%s), %s, $options + $this->options)', $definition['route'], $definition['key'], $definition['key2'], str_replace("\n", '', var_export(isset($definition['data']) ? $definition['data'] : [], true)));

        $zMethod->setBody(
            'return '.$returnBody.';'
        );
    }
    /**
     * @param MethodGenerator $zMethod
     * @param array           $definition
     *
     * @Annotation\CodeGeneratorMethodType("crud.clearProperties")
     */
    public function generateCrudClearPropertiesMethod(MethodGenerator $zMethod, $definition = [])
    {
        $definition['route'] = str_replace(['{id}', '{property}'], ['%s', '%s'], $definition['route']);

        $zMethod->setDocBlock(new DocBlockGenerator(
            sprintf('Clear the specified %s properties', $definition['type']),
            null,
            [
                new ParamTag('id', ['mixed'], sprintf('ID of the %s', $definition['type'])),
                new ParamTag('properties', ['array'], 'The list of properties to clear'),
                new ParamTag('options', ['array'], 'Options'),
                new ReturnTag(['mixed']),
                new ThrowsTag(['\\Exception'], 'if an error occured'),
            ]
        ));
        $zMethod->setParameters([
            new ParameterGenerator('id', 'string'),
            new ParameterGenerator('properties', 'array'),
            new ParameterGenerator('options', 'array', []),
        ]);

        $returnBody = sprintf('$this->getSdk()->delete(sprintf(\'%s\', $id, join(\',\', array_values($properties))), %s, $options + $this->options)', $definition['route'], str_replace("\n", '', var_export(isset($definition['data']) ? $definition['data'] : [], true)));

        $zMethod->setBody(
            'return '.$returnBody.';'
        );
    }
    /**
     * @param MethodGenerator $zMethod
     * @param array           $definition
     *
     * @Annotation\CodeGeneratorMethodType("crud.resetPropertyBy")
     */
    public function generateCrudResetPropertyByMethod(MethodGenerator $zMethod, $definition = [])
    {
        $definition['route'] = str_replace(sprintf('{%s}', $definition['key']), '%s', $definition['route']);

        $zMethod->setDocBlock(new DocBlockGenerator(
            sprintf('Reset the %s %s by %s %s', $definition['type'], $definition['property'], $definition['type'], $definition['key']),
            null,
            [
                new ParamTag($definition['key'], ['string'], sprintf('%s of the %s', $definition['key'], $definition['type'])),
                new ParamTag('options', ['array'], 'Options'),
                new ReturnTag(['array']),
                new ThrowsTag(['\\Exception'], 'if an error occured'),
            ]
        ));
        $zMethod->setParameters([
            new ParameterGenerator($definition['key'], 'string'),
            new ParameterGenerator('options', 'array', []),
        ]);
        $zMethod->setBody(
            sprintf('return $this->getSdk()->delete(sprintf(\'%s\', $%s), $options + $this->options);', $definition['route'], $definition['key'])
        );
    }
}
