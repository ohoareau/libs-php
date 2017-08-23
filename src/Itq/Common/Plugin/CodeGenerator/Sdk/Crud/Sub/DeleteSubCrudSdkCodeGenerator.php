<?php

/*
 * This file is part of the COMMON package.
 *
 * (c) itiQiti SAS <opensource@itiqiti.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Itq\Common\Plugin\CodeGenerator\Sdk\Crud\Sub;

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
class DeleteSubCrudSdkCodeGenerator extends Base\AbstractSubCrudSdkCodeGenerator
{
    /**
     * @param MethodGenerator $zMethod
     * @param array           $definition
     *
     * @Annotation\CodeGeneratorMethodType("crud.sub.delete")
     */
    public function generateCrudSubDeleteMethod(MethodGenerator $zMethod, $definition = [])
    {
        $definition['route'] = str_replace(['{parentId}', '{id}'], '%s', $definition['route']);

        $zMethod->setDocBlock(new DocBlockGenerator(
            sprintf('Delete the specified %s %s by %s id', $definition['type'], $definition['subType'], $definition['type']),
            null,
            [
                new ParamTag('parentId', ['mixed'], sprintf('ID of the %s', $definition['type'])),
                new ParamTag('id', ['mixed'], sprintf('ID of the %s', $definition['subType'])),
                new ParamTag('options', ['array'], 'Options'),
                new ReturnTag(['array']),
                new ThrowsTag(['\\Exception'], 'if an error occured'),
            ]
        ));
        $zMethod->setParameters([
            new ParameterGenerator('parentId', 'string'),
            new ParameterGenerator('id', 'string'),
            new ParameterGenerator('options', 'array', []),
        ]);
        $zMethod->setBody(
            sprintf('return $this->getSdk()->delete(sprintf(\'%s\', $parentId, $id), $options + $this->options);', $definition['route'])
        );
    }
    /**
     * @param MethodGenerator $zMethod
     * @param array           $definition
     *
     * @Annotation\CodeGeneratorMethodType("crud.sub.purge")
     */
    public function generateCrudSubPurgeMethod(MethodGenerator $zMethod, $definition = [])
    {
        $zMethod->setDocBlock(new DocBlockGenerator(
            sprintf('Purge all %s %ss by %s id', $definition['type'], $definition['subType'], $definition['type']),
            null,
            [
                new ParamTag('parentId', ['string'], 'Id of the '.$definition['type']),
                new ParamTag('criteria', ['array'], 'Optional criteria to filter deleteds'),
                new ParamTag('options', ['array'], 'Options'),
                new ReturnTag(['array']),
                new ThrowsTag(['\\Exception'], 'if an error occured'),
            ]
        ));
        $zMethod->setParameters([
            new ParameterGenerator('parentId', 'string'),
            new ParameterGenerator('criteria', 'array', []),
            new ParameterGenerator('options', 'array', []),
        ]);
        $zMethod->setBody(
            sprintf('return $this->getSdk()->purge(sprintf(\'%s\', $parentId), $criteria, $options + $this->options);', $definition['route'])
        );
    }
    /**
     * @param MethodGenerator $zMethod
     * @param array           $definition
     *
     * @Annotation\CodeGeneratorMethodType("crud.sub.clearProperties")
     */
    public function generateCrudSubClearPropertiesMethod(MethodGenerator $zMethod, $definition = [])
    {
        $definition['route'] = str_replace(['{parentId}', '{id}', '{property}'], '%s', $definition['route']);

        $zMethod->setDocBlock(new DocBlockGenerator(
            sprintf('Clear the specified %s properties', $definition['type']),
            null,
            [
                new ParamTag('parentId', ['mixed'], sprintf('ID of the %s', $definition['type'])),
                new ParamTag('id', ['mixed'], sprintf('ID of the %s', $definition['subType'])),
                new ParamTag('properties', ['array'], 'The list of properties to clear'),
                new ParamTag('options', ['array'], 'Options'),
                new ReturnTag(['mixed']),
                new ThrowsTag(['\\Exception'], 'if an error occured'),
            ]
        ));
        $zMethod->setParameters([
            new ParameterGenerator('parentId', 'string'),
            new ParameterGenerator('id', 'string'),
            new ParameterGenerator('properties', 'array'),
            new ParameterGenerator('options', 'array', []),
        ]);

        $returnBody = sprintf('$this->getSdk()->delete(sprintf(\'%s\', $parentId, $id, join(\',\', array_values($properties))), %s, $options + $this->options)', $definition['route'], str_replace("\n", '', var_export(isset($definition['data']) ? $definition['data'] : [], true)));

        $zMethod->setBody(
            'return '.$returnBody.';'
        );
    }
    /**
     * @param MethodGenerator $zMethod
     * @param array           $definition
     *
     * @Annotation\CodeGeneratorMethodType("crud.sub.clearProperty")
     */
    public function generateCrudSubClearPropertyMethod(MethodGenerator $zMethod, $definition = [])
    {
        $definition['route'] = str_replace(['{parentId}', '{id}', '{property}'], ['%s', '%s', $definition['property']], $definition['route']);

        $zMethod->setDocBlock(new DocBlockGenerator(
            sprintf('Clear the specified %s %s', $definition['type'], $definition['property']),
            null,
            [
                new ParamTag('parentId', ['mixed'], sprintf('ID of the %s', $definition['type'])),
                new ParamTag('id', ['mixed'], sprintf('ID of the %s', $definition['subType'])),
                new ParamTag('options', ['array'], 'Options'),
                new ReturnTag(['mixed']),
                new ThrowsTag(['\\Exception'], 'if an error occured'),
            ]
        ));
        $zMethod->setParameters([
            new ParameterGenerator('parentId', 'string'),
            new ParameterGenerator('id', 'string'),
            new ParameterGenerator('options', 'array', []),
        ]);

        $returnBody = sprintf('$this->getSdk()->delete(sprintf(\'%s\', $parentId, $id), %s, $options + $this->options)', $definition['route'], str_replace("\n", '', var_export(isset($definition['data']) ? $definition['data'] : [], true)));

        $zMethod->setBody(
            'return '.$returnBody.';'
        );
    }
    /**
     * @param MethodGenerator $zMethod
     * @param array           $definition
     *
     * @Annotation\CodeGeneratorMethodType("crud.sub.purgeAndCreateBulk")
     */
    public function generateCrudSubPurgeAndCreateBulkMethod(MethodGenerator $zMethod, $definition = [])
    {
        $definition += ['subType' => 'subType'];
        $definition['route'] = str_replace('{parentId}', '%s', $definition['route']);
        $model = isset($definition['model']) ? (is_bool($definition['model']) ? $definition['returnType']['type'] : $definition['model']) : null;

        $zMethod->setDocBlock(new DocBlockGenerator(
            sprintf('Purge and create new %s %ss by %s id', $definition['type'], $definition['subType'], $definition['type']),
            null,
            [
                new ParamTag('parentId', ['string'], sprintf('Id of the %s', $definition['type'])),
                new ParamTag('data', ['array'], 'Data to store'),
                new ParamTag('options', ['array'], 'Options'),
                new ReturnTag([$model ?: 'array']),
                new ThrowsTag(['\\Exception'], 'if an error occured'),
            ]
        ));

        $zMethod->setParameters([
            new ParameterGenerator('parentId', 'string'),
            new ParameterGenerator('data', 'array', []),
            new ParameterGenerator('options', 'array', []),
        ]);

        $returnBody = sprintf('$this->getSdk()->update(sprintf(\'%s\', $parentId), %s + $data, $options + $this->options)', $definition['route'], str_replace("\n", '', var_export(isset($definition['data']) ? $definition['data'] : [], true)));
        $zMethod->setBody(
            'return '.($model ? ('$this->modelize('.$returnBody.', $options + $this->options + [\'model\' => \''.$model.'\'])') : $returnBody).';'
        );
    }
}
