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
class CreateSubCrudSdkCodeGenerator extends Base\AbstractSubCrudSdkCodeGenerator
{
    /**
     * @param MethodGenerator $zMethod
     * @param array           $definition
     *
     * @Annotation\CodeGeneratorMethodType("crud.sub.createBy")
     */
    public function generateCrudSubCreateByMethod(MethodGenerator $zMethod, $definition = [])
    {
        $definition += ['field' => 'id', 'subType' => 'subType'];
        $definition['route'] = str_replace('{'.$definition['field'].'}', '%s', $definition['route']);

        $zMethod->setDocBlock(new DocBlockGenerator(
            sprintf('Create a new %s %s by %s', $definition['type'], $definition['subType'], $definition['field']),
            null,
            [
                new ParamTag($definition['field'], ['mixed'], sprintf('%s of the %s', ucfirst($definition['field']), $definition['type'])),
                new ParamTag('data', ['array'], 'Data to store'),
                new ParamTag('options', ['array'], 'Options'),
                new ReturnTag(['array']),
                new ThrowsTag(['\\Exception'], 'if an error occured'),
            ]
        ));
        $zMethod->setParameters([
            new ParameterGenerator($definition['field'], 'mixed'),
            new ParameterGenerator('data', 'array', []),
            new ParameterGenerator('options', 'array', []),
        ]);
        $zMethod->setBody(
            sprintf('return $this->getSdk()->create(sprintf(\'%s\', $%s), $data, $options + $this->options);', $definition['route'], $definition['field'])
        );
    }
    /**
     * @param MethodGenerator $zMethod
     * @param array           $definition
     *
     * @Annotation\CodeGeneratorMethodType("crud.sub.create")
     */
    public function generateCrudSubCreateMethod(MethodGenerator $zMethod, $definition = [])
    {
        $definition += ['subType' => 'subType'];
        $definition['route'] = str_replace('{parentId}', '%s', $definition['route']);
        $model = isset($definition['model']) ? (is_bool($definition['model']) ? $definition['returnType']['type'] : $definition['model']) : null;

        $zMethod->setDocBlock(new DocBlockGenerator(
            sprintf('Create a new %s %s by %s id', $definition['type'], $definition['subType'], $definition['type']),
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

        $returnBody = sprintf('$this->getSdk()->create(sprintf(\'%s\', $parentId), %s + $data, $options + $this->options)', $definition['route'], str_replace("\n", '', var_export(isset($definition['data']) ? $definition['data'] : [], true)));
        $zMethod->setBody(
            'return '.($model ? ('$this->modelize('.$returnBody.', $options + $this->options + [\'model\' => \''.$model.'\'])') : $returnBody).';'
        );
    }
}
