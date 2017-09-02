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
class UpdateCrudSdkCodeGenerator extends Base\AbstractCrudSdkCodeGenerator
{
    /**
     * @param MethodGenerator $zMethod
     * @param array           $definition
     *
     * @Annotation\CodeGeneratorMethodType("crud.update")
     */
    public function generateCrudUpdateMethod(MethodGenerator $zMethod, $definition = [])
    {
        $model = isset($definition['model']) ? (is_bool($definition['model']) ? $definition['returnType']['type'] : $definition['model']) : null;

        $definition['route'] = str_replace('{id}', '%s', $definition['route']);

        $zMethod->setDocBlock(new DocBlockGenerator(
            sprintf('Update the specified %s', $definition['type']),
            null,
            [
                new ParamTag('id', ['mixed'], sprintf('ID of the %s', $definition['type'])),
                new ParamTag('data', ['array'], 'Data to update'),
                new ParamTag('options', ['array'], 'Options'),
                new ReturnTag([$model ?: 'array']),
                new ThrowsTag(['\\Exception'], 'if an error occured'),
            ]
        ));
        $zMethod->setParameters([
            new ParameterGenerator('id', 'string'),
            new ParameterGenerator('data', 'array', []),
            new ParameterGenerator('options', 'array', []),
        ]);

        $returnBody = sprintf('$this->getSdk()->update(sprintf(\'%s\', $id), %s + $data, $options + $this->options)', $definition['route'], str_replace("\n", '', var_export(isset($definition['data']) ? $definition['data'] : [], true)));

        $zMethod->setBody(
            'return '.($model ? ('$this->modelize('.$returnBody.', $options + $this->options + [\'model\' => \''.$model.'\'])') : $returnBody).';'
        );
    }
    /**
     * @param MethodGenerator $zMethod
     * @param array           $definition
     *
     * @Annotation\CodeGeneratorMethodType("crud.updateProperty")
     */
    public function generateCrudUpdatePropertyMethod(MethodGenerator $zMethod, $definition = [])
    {
        $definition['route'] = str_replace('{id}', '%s', $definition['route']);

        $zMethod->setDocBlock(new DocBlockGenerator(
            sprintf('Update %s of the specified %s', $definition['property'], $definition['type']),
            null,
            [
                new ParamTag('id', ['mixed'], sprintf('ID of the %s', $definition['type'])),
                new ParamTag('data', ['mixed'], sprintf('Value for the %s', $definition['property'])),
                new ParamTag('options', ['array'], 'Options'),
                new ReturnTag(['array']),
                new ThrowsTag(['\\Exception'], 'if an error occured'),
            ]
        ));
        $zMethod->setParameters([
            new ParameterGenerator('id', 'string'),
            new ParameterGenerator('data', 'mixed', []),
            new ParameterGenerator('options', 'array', []),
        ]);
        $zMethod->setBody(
            sprintf('return $this->getSdk()->update(sprintf(\'%s\', $id), %s + $data, $options + $this->options);', $definition['route'], str_replace("\n", '', var_export(isset($definition['data']) ? $definition['data'] : [], true)))
        );
    }
    /**
     * @param MethodGenerator $zMethod
     * @param array           $definition
     *
     * @Annotation\CodeGeneratorMethodType("crud.updateBy")
     */
    public function generateCrudUpdateByMethod(MethodGenerator $zMethod, $definition = [])
    {
        if (!is_array($definition['key'])) {
            $definition['key'] = [$definition['key']];
        }

        $keyParamTags = [];
        $keyParams    = [];
        $keyRouteParams = [];

        foreach ($definition['key'] as $key) {
            $definition['route'] = str_replace('{'.$key.'}', '%s', $definition['route']);
            $keyParamTags[] = new ParamTag($key, ['string'], sprintf('%s of the %s', $key, $definition['type']));
            $keyParams[]    = new ParameterGenerator($key, 'string');
            if (isset($definition['transform']['key'.ucfirst($key)])) {
                switch ($definition['transform']['key'.ucfirst($key)]) {
                    case 'base64':
                        $keyRouteParams[] = "base64_encode(\$$key)";
                        break;
                    default:
                        throw new \RuntimeException(sprintf("Unsupported key formatter: %s", $definition['transform'][sprintf('key%s', ucfirst($key))]), 500);
                }
            } else {
                $keyRouteParams[] = "\$$key";
            }
        }

        $zMethod->setDocBlock(new DocBlockGenerator(
            sprintf('Update %s specified by its %s', $definition['type'], join(' and ', $definition['key'])),
            null,
            array_merge(
                $keyParamTags,
                [
                    new ParamTag('data', ['array'], 'Data to update'),
                    new ParamTag('options', ['array'], 'Options'),
                    new ReturnTag(['array']),
                    new ThrowsTag(['\\Exception'], 'if an error occured'),
                ]
            )
        ));
        $zMethod->setParameters(
            array_merge(
                $keyParams,
                [
                    new ParameterGenerator('data', 'array', []),
                    new ParameterGenerator('options', 'array', []),
                ]
            )
        );
        $zMethod->setBody(
            sprintf('return $this->getSdk()->update(sprintf(\'%s\', %s), %s + $data, $options);', $definition['route'], join(', ', $keyRouteParams), str_replace("\n", '', var_export(isset($definition['data']) ? $definition['data'] : [], true)))
        );
    }
    /**
     * @param MethodGenerator $zMethod
     * @param array           $definition
     *
     * @Annotation\CodeGeneratorMethodType("crud.updateBy2KeysAndReturnDoubleBagBy")
     */
    public function generateCrudUpdateBy2KeysAndReturnDoubleBagByMethod(MethodGenerator $zMethod, $definition = [])
    {
        $model = isset($definition['model']) ? (is_bool($definition['model']) ? $definition['returnType']['type'] : $definition['model']) : null;
        $definition['route'] = str_replace(['{'.$definition['key'].'}', '{'.$definition['key2'].'}'], '%s', $definition['route']);

        $zMethod->setDocBlock(new DocBlockGenerator(
            sprintf('Update %s specified by its %s and its %s', $definition['type'], $definition['key'], $definition['key2']),
            null,
            [
                new ParamTag($definition['key'], ['string'], sprintf('%s of the %s', $definition['key'], $definition['type'])),
                new ParamTag($definition['key2'], ['string'], sprintf('%s of the %s', $definition['key2'], $definition['type'])),
                new ParamTag('data', ['array'], 'Data to update'),
                new ParamTag('fields', ['array'], sprintf('Fields to retrieve for the %s', $definition['type'])),
                new ParamTag(sprintf('%sFields', $definition['type2']), ['array'], sprintf('Fields to retrieve for the %s', $definition['type2'])),
                new ParamTag('options', ['array'], 'Options'),
                new ReturnTag([$model ?: 'array']),
                new ThrowsTag(['\\Exception'], 'if an error occured'),
            ]
        ));
        $zMethod->setParameters([
            new ParameterGenerator($definition['key'], 'string'),
            new ParameterGenerator($definition['key2'], 'string'),
            new ParameterGenerator('data', 'array', []),
            new ParameterGenerator('fields', 'array', []),
            new ParameterGenerator(sprintf('%sFields', $definition['type2']), 'array', []),
            new ParameterGenerator('options', 'array', []),
        ]);

        $returnBody = sprintf('$this->getSdk()->update(sprintf(\'%s\', $%s, $%s), %s + $data, [\'fields\' => $fields, \'otherFieldBags\' => [\'%sFields\' => $%sFields]] + $options)', $definition['route'], $definition['key'], $definition['key2'], str_replace("\n", '', var_export(isset($definition['data']) ? $definition['data'] : [], true)), $definition['type2'], $definition['type2']);

        $zMethod->setBody(
            'return '.($model ? ('$this->modelize('.$returnBody.', $options + $this->options + [\'model\' => \''.$model.'\'])') : $returnBody).';'
        );
    }
    /**
     * @param MethodGenerator $zMethod
     * @param array           $definition
     *
     * @Annotation\CodeGeneratorMethodType("crud.updateBy2KeysAndReturnBy")
     */
    public function generateCrudUpdateBy2KeysAndReturnByMethod(MethodGenerator $zMethod, $definition = [])
    {
        $model = isset($definition['model']) ? (is_bool($definition['model']) ? $definition['returnType']['type'] : $definition['model']) : null;
        $definition['route'] = str_replace(['{'.$definition['key'].'}', '{'.$definition['key2'].'}'], '%s', $definition['route']);

        $zMethod->setDocBlock(new DocBlockGenerator(
            sprintf('Update %s specified by its %s and its %s', $definition['type'], $definition['key'], $definition['key2']),
            null,
            [
                new ParamTag($definition['key'], ['string'], sprintf('%s of the %s', $definition['key'], $definition['type'])),
                new ParamTag($definition['key2'], ['string'], sprintf('%s of the %s', $definition['key2'], $definition['type'])),
                new ParamTag('data', ['array'], 'Data to update'),
                new ParamTag('fields', ['array'], sprintf('Fields to retrieve for the %s', $definition['type'])),
                new ParamTag('options', ['array'], 'Options'),
                new ReturnTag([$model ?: 'array']),
                new ThrowsTag(['\\Exception'], 'if an error occured'),
            ]
        ));
        $zMethod->setParameters([
            new ParameterGenerator($definition['key'], 'string'),
            new ParameterGenerator($definition['key2'], 'string'),
            new ParameterGenerator('data', 'array', []),
            new ParameterGenerator('fields', 'array', []),
            new ParameterGenerator('options', 'array', []),
        ]);

        $returnBody = sprintf('$this->getSdk()->update(sprintf(\'%s\', $%s, $%s), %s + $data, [\'fields\' => $fields] + $options)', $definition['route'], $definition['key'], $definition['key2'], str_replace("\n", '', var_export(isset($definition['data']) ? $definition['data'] : [], true)));

        $zMethod->setBody(
            'return '.($model ? ('$this->modelize('.$returnBody.', $options + $this->options + [\'model\' => \''.$model.'\'])') : $returnBody).';'
        );
    }
}
