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
class GetCrudSdkCodeGenerator extends Base\AbstractCrudSdkCodeGenerator
{
    /**
     * @param MethodGenerator $zMethod
     * @param array           $definition
     *
     * @Annotation\CodeGeneratorMethodType("crud.get")
     */
    public function generateCrudGetMethod(MethodGenerator $zMethod, $definition = [])
    {
        $model = isset($definition['model']) ? (is_bool($definition['model']) ? $definition['returnType']['type'] : $definition['model']) : null;

        $definition['route'] = str_replace('{id}', '%s', $definition['route']);

        $zMethod->setDocBlock(new DocBlockGenerator(
            sprintf('Return the specified %s', $definition['type']),
            null,
            [
                new ParamTag('id', ['mixed'], sprintf('ID of the %s', $definition['type'])),
                new ParamTag('fields', ['array'], 'List of fields to retrieve'),
                new ParamTag('options', ['array'], 'Options'),
                new ReturnTag([$model ?: 'array']),
                new ThrowsTag(['\\Exception'], 'if an error occured'),
            ]
        ));
        $zMethod->setParameters([
            new ParameterGenerator('id', 'string'),
            new ParameterGenerator('fields', 'array', []),
            new ParameterGenerator('options', 'array', []),
        ]);

        $returnBody = sprintf('$this->getSdk()->get(sprintf(\'%s\', $id), $fields, $options + $this->options)', $definition['route'], isset($definition['route']) ? $definition['route'] : null);

        $zMethod->setBody(
            'return '.($model ? ('$this->modelize('.$returnBody.', $options + $this->options + [\'model\' => \''.$model.'\'])') : $returnBody).';'
        );
    }
    /**
     * @param MethodGenerator $zMethod
     * @param array           $definition
     *
     * @Annotation\CodeGeneratorMethodType("crud.getBy")
     */
    public function generateCrudGetByMethod(MethodGenerator $zMethod, $definition = [])
    {
        $model = isset($definition['model']) ? (is_bool($definition['model']) ? $definition['returnType']['type'] : $definition['model']) : null;

        $definition += ['field' => 'id'];
        $definition['route'] = str_replace('{'.$definition['field'].'}', '%s', $definition['route']);

        $zMethod->setDocBlock(new DocBlockGenerator(
            sprintf('Return the specified %s by %s', $definition['type'], $definition['field']),
            null,
            [
                new ParamTag($definition['field'], ['mixed'], sprintf(ucfirst($definition['field']).' of the %s', $definition['type'])),
                new ParamTag('fields', ['array'], 'List of fields to retrieve'),
                new ParamTag('options', ['array'], 'Options'),
                new ReturnTag([$model ?: 'array']),
                new ThrowsTag(['\\Exception'], 'if an error occured'),
            ]
        ));
        $zMethod->setParameters([
            new ParameterGenerator($definition['field'], 'string'),
            new ParameterGenerator('fields', 'array', []),
            new ParameterGenerator('options', 'array', []),
        ]);

        $returnBody = sprintf('$this->getSdk()->get(sprintf(\'%s\', $%s), $fields, $options + $this->options)', $definition['route'], $definition['field']);

        $zMethod->setBody(
            'return '.($model ? ('$this->modelize('.$returnBody.', $options + $this->options + [\'model\' => \''.$model.'\'])') : $returnBody).';'
        );
    }
    /**
     * @param MethodGenerator $zMethod
     * @param array           $definition
     *
     * @Annotation\CodeGeneratorMethodType("crud.getDoubleBagBy")
     */
    public function generateCrudGetDoubleBagByMethod(MethodGenerator $zMethod, $definition = [])
    {
        $model = isset($definition['model']) ? (is_bool($definition['model']) ? $definition['returnType']['type'] : $definition['model']) : null;

        $definition += ['field' => 'id'];
        $definition['route'] = str_replace('{'.$definition['field'].'}', '%s', $definition['route']);

        $zMethod->setDocBlock(new DocBlockGenerator(
            sprintf('Return the %s and the associated %s specified by %s %s', $definition['type'], $definition['type2'], $definition['type'], $definition['field']),
            null,
            [
                new ParamTag($definition['field'], ['mixed'], sprintf(ucfirst($definition['field']).' of the %s', $definition['type'])),
                new ParamTag('fields', ['array'], sprintf('List of fields of the %s to retrieve', $definition['type'])),
                new ParamTag(sprintf('%sFields', $definition['type2']), ['array'], sprintf('List of fields of the %s to retrieve', $definition['type2'])),
                new ParamTag('options', ['array'], 'Options'),
                new ReturnTag([$model ?: 'array']),
                new ThrowsTag(['\\Exception'], 'if an error occured'),
            ]
        ));
        $zMethod->setParameters([
            new ParameterGenerator($definition['field'], 'string'),
            new ParameterGenerator('fields', 'array', []),
            new ParameterGenerator(sprintf('%sFields', $definition['type2']), 'array', []),
            new ParameterGenerator('options', 'array', []),
        ]);

        $returnBody = sprintf('$this->getSdk()->get(sprintf(\'%s\', $%s), $fields, [\'otherFieldBags\' => [\'%sFields\' => $%sFields]] + $options + $this->options)', $definition['route'], $definition['field'], $definition['type2'], $definition['type2']);

        $zMethod->setBody(
            'return '.($model ? ('$this->modelize('.$returnBody.', $options + $this->options + [\'model\' => \''.$model.'\'])') : $returnBody).';'
        );
    }
    /**
     * @param MethodGenerator $zMethod
     * @param array           $definition
     *
     * @Annotation\CodeGeneratorMethodType("crud.getPropertyBy")
     */
    public function generateCrudGetPropertyByMethod(MethodGenerator $zMethod, $definition = [])
    {
        $definition += ['field' => 'id', 'property' => 'id'];
        $definition['route'] = str_replace(['{'.$definition['field'].'}', '{'.$definition['property'].'}'], '%s', $definition['route']);

        $zMethod->setDocBlock(new DocBlockGenerator(
            sprintf('Return the specified %s %s by %s', $definition['type'], $definition['property'], $definition['field']),
            null,
            [
                new ParamTag($definition['field'], ['mixed'], sprintf(ucfirst($definition['field']).' of the %s', $definition['type'])),
                new ParamTag('options', ['array'], 'Options'),
                new ReturnTag(['mixed']),
                new ThrowsTag(['\\Exception'], 'if an error occured'),
            ]
        ));
        $zMethod->setParameters([
            new ParameterGenerator($definition['field'], 'string'),
            new ParameterGenerator('options', 'array', []),
        ]);
        $zMethod->setBody(
            sprintf('return $this->getSdk()->get(sprintf(\'%s\', $%s), [], $options + $this->options);', $definition['route'], $definition['field'])
        );
    }
    /**
     * @param MethodGenerator $zMethod
     * @param array           $definition
     *
     * @Annotation\CodeGeneratorMethodType("crud.getPropertyPathBy")
     */
    public function generateCrudGetPropertyPathByMethod(MethodGenerator $zMethod, $definition = [])
    {
        $definition += ['field' => 'id', 'property' => 'id'];
        $definition['route'] = str_replace(['{'.$definition['field'].'}', '{'.$definition['property'].'}'], '%s', $definition['route']);

        $zMethod->setDocBlock(new DocBlockGenerator(
            sprintf('Return the local path of the file containing the specified %s %s by %s', $definition['type'], $definition['property'], $definition['field']),
            null,
            [
                new ParamTag($definition['field'], ['mixed'], sprintf(ucfirst($definition['field']).' of the %s', $definition['type'])),
                new ParamTag('options', ['array'], 'Options'),
                new ReturnTag(['mixed']),
                new ThrowsTag(['\\Exception'], 'if an error occured'),
            ]
        ));
        $zMethod->setParameters([
            new ParameterGenerator($definition['field'], 'string'),
            new ParameterGenerator('options', 'array', []),
        ]);
        $zMethod->setBody(
            sprintf('return $this->getSdk()->getPath(sprintf(\'%s\', $%s), [], [\'format\' => \'%s\'] + $options + $this->options);', $definition['route'], $definition['field'], $definition['format'])
        );
    }
    /**
     * @param MethodGenerator $zMethod
     * @param array           $definition
     *
     * @Annotation\CodeGeneratorMethodType("crud.getBy2Keys")
     */
    public function generateCrudGetBy2KeysMethod(MethodGenerator $zMethod, $definition = [])
    {
        $model = isset($definition['model']) ? (is_bool($definition['model']) ? $definition['returnType']['type'] : $definition['model']) : null;
        $definition['route'] = str_replace(['{'.$definition['key'].'}', '{'.$definition['key2'].'}'], '%s', $definition['route']);

        $zMethod->setDocBlock(new DocBlockGenerator(
            sprintf('Return %s specified by its %s and its %s', $definition['type'], $definition['key'], $definition['key2']),
            null,
            [
                new ParamTag($definition['key'], ['string'], sprintf('%s of the %s', $definition['key'], $definition['type'])),
                new ParamTag($definition['key2'], ['string'], sprintf('%s of the %s', $definition['key2'], $definition['type'])),
                new ParamTag('fields', ['array'], sprintf('Fields to retrieve for the %s', $definition['type'])),
                new ParamTag('options', ['array'], 'Options'),
                new ReturnTag([$model ?: 'array']),
                new ThrowsTag(['\\Exception'], 'if an error occured'),
            ]
        ));
        $zMethod->setParameters([
            new ParameterGenerator($definition['key'], 'string'),
            new ParameterGenerator($definition['key2'], 'string'),
            new ParameterGenerator('fields', 'array', []),
            new ParameterGenerator('options', 'array', []),
        ]);

        $returnBody = sprintf('$this->getSdk()->get(sprintf(\'%s\', $%s, $%s), $fields, $options)', $definition['route'], $definition['key'], $definition['key2']);

        $zMethod->setBody(
            'return '.($model ? ('$this->modelize('.$returnBody.', $options + $this->options + [\'model\' => \''.$model.'\'])') : $returnBody).';'
        );
    }
}
