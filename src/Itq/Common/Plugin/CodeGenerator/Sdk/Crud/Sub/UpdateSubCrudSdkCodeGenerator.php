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
class UpdateSubCrudSdkCodeGenerator extends Base\AbstractSubCrudSdkCodeGenerator
{
    /**
     * @param MethodGenerator $zMethod
     * @param array           $definition
     *
     * @Annotation\CodeGeneratorMethodType("crud.sub.update")
     */
    public function generateCrudSubUpdateMethod(MethodGenerator $zMethod, $definition = [])
    {
        $model = isset($definition['model']) ? (is_bool($definition['model']) ? $definition['returnType']['type'] : $definition['model']) : null;

        $definition['route'] = str_replace(['{parentId}', '{id}'], '%s', $definition['route']);

        $zMethod->setDocBlock(new DocBlockGenerator(
            sprintf('Update the specified %s %s by %s id', $definition['type'], $definition['subType'], $definition['type']),
            null,
            [
                new ParamTag('parentId', ['mixed'], sprintf('ID of the %s', $definition['type'])),
                new ParamTag('id', ['mixed'], sprintf('ID of the %s', $definition['subType'])),
                new ParamTag('data', ['array'], 'Data to update'),
                new ParamTag('options', ['array'], 'Options'),
                new ReturnTag([$model ?: 'array']),
                new ThrowsTag(['\\Exception'], 'if an error occured'),
            ]
        ));
        $zMethod->setParameters([
            new ParameterGenerator('parentId', 'string'),
            new ParameterGenerator('id', 'string'),
            new ParameterGenerator('data', 'array', []),
            new ParameterGenerator('options', 'array', []),
        ]);

        $returnBody = sprintf('$this->getSdk()->update(sprintf(\'%s\', $parentId, $id), %s + $data, $options + $this->options)', $definition['route'], str_replace("\n", '', var_export(isset($definition['data']) ? $definition['data'] : [], true)));

        $zMethod->setBody(
            'return '.($model ? ('$this->modelize('.$returnBody.', $options + $this->options + [\'model\' => \''.$model.'\'])') : $returnBody).';'
        );
    }
    /**
     * @param MethodGenerator $zMethod
     * @param array           $definition
     *
     * @Annotation\CodeGeneratorMethodType("crud.sub.setPropertyBy")
     */
    public function generateCrudSubSetPropertyByMethod(MethodGenerator $zMethod, $definition = [])
    {
        $definition['route'] = str_replace(['{id}', '{'.$definition['key'].'}'], ['%s', '%s'], $definition['route']);

        $zMethod->setDocBlock(new DocBlockGenerator(
            sprintf('Set the %s of the %s %s to %s', $definition['property'], $definition['type'], $definition['subType'], $definition['value']),
            null,
            [
                new ParamTag($definition['key'], ['string'], sprintf($definition['key'].' of the %s', $definition['type'])),
                new ParamTag('id', ['mixed'], sprintf('ID of the %s %s', $definition['type'], $definition['subType'])),
                new ParamTag('data', ['array'], 'Optional extra data'),
                new ParamTag('options', ['array'], 'Options'),
                new ReturnTag(['array']),
                new ThrowsTag(['\\Exception'], 'if an error occured'),
            ]
        ));
        $zMethod->setParameters([
            new ParameterGenerator($definition['key'], 'string'),
            new ParameterGenerator('id', 'string'),
            new ParameterGenerator('data', 'array', []),
            new ParameterGenerator('options', 'array', []),
        ]);
        $zMethod->setBody(
            sprintf('return $this->getSdk()->update(sprintf(\'%s\', $%s, $id), [\'%s\' => %s] + $data, $options + $this->options);', $definition['route'], $definition['key'], $definition['property'], str_replace("\n", '', var_export($definition['value'], true)))
        );
    }
}
