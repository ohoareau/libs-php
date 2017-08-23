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

use Zend\Code\Generator\ValueGenerator;
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
class ImportCrudSdkCodeGenerator extends Base\AbstractCrudSdkCodeGenerator
{
    /**
     * @param MethodGenerator $zMethod
     * @param array           $definition
     *
     * @Annotation\CodeGeneratorMethodType("crud.importFor")
     */
    public function generateCrudImportForMethod(MethodGenerator $zMethod, $definition = [])
    {
        $model = isset($definition['model']) ? (is_bool($definition['model']) ? '\\TombolaDirecte\\Bundle\\SdkBundle\\Model\\Response\\Util\\ImportResult' : $definition['model']) : null;

        $zMethod->setDocBlock(new DocBlockGenerator(
            sprintf('Import %ss for specified %s', $definition['type'], $definition['for']),
            null,
            [
                new ParamTag($definition['for'], ['string'], sprintf('The %s for which to import the %ss', $definition['for'], $definition['type'])),
                new ParamTag('bulkData', ['array'], 'The bulk data to store'),
                new ParamTag('keys', ['array'], 'The list of fields to use to compute the unique identifier of an item'),
                new ParamTag('fields', ['array'], 'The list of updatable fields to take into account'),
                new ParamTag('progressToken', ['string'], 'The optional progress token to update job progress'),
                new ParamTag('commonData', ['array'], 'The optional common data to use for each item'),
                new ParamTag('options', ['array'], 'Options'),
                new ReturnTag([$model ?: 'array']),
                new ThrowsTag(['\\Exception'], 'if an error occured'),
            ]
        ));
        $zMethod->setParameters([
            new ParameterGenerator($definition['for'], 'string'),
            new ParameterGenerator('bulkData', 'array', []),
            new ParameterGenerator('keys', 'array', []),
            new ParameterGenerator('fields', 'array', []),
            new ParameterGenerator('commonData', 'array', []),
            new ParameterGenerator('progressToken', 'string', new ValueGenerator(null)),
            new ParameterGenerator('options', 'array', []),
        ]);

        $returnBody = sprintf('$this->getSdk()->create(\'%s\', [\'settings\' => [\'keys\' => array_merge(array_keys([\'%s\' => $%s] + $commonData), $keys), \'fields\' => $fields, \'common\' => [\'%s\' => $%s] + $commonData, \'progressToken\' => $progressToken], \'data\' => $bulkData], %s + $options + $this->options)', $definition['route'], $definition['for'], $definition['for'], $definition['for'], $definition['for'], str_replace("\n", '', var_export(['import' => true] + (isset($definition['options']) ? $definition['options'] : []), true)));
        $zMethod->setBody(
            sprintf('return '.($model ? ('$this->modelize('.$returnBody.', $options + $this->options + [\'model\' => \''.$model.'\'])') : $returnBody)).';'
        );
    }
    /**
     * @param MethodGenerator $zMethod
     * @param array           $definition
     *
     * @Annotation\CodeGeneratorMethodType("crud.import")
     */
    public function generateCrudImportMethod(MethodGenerator $zMethod, $definition = [])
    {
        $model = isset($definition['model']) ? (is_bool($definition['model']) ? '\\TombolaDirecte\\Bundle\\SdkBundle\\Model\\Response\\Util\\ImportResult' : $definition['model']) : null;

        $zMethod->setDocBlock(new DocBlockGenerator(
            sprintf('Import %ss', $definition['type']),
            null,
            [
                new ParamTag('bulkData', ['array'], 'The bulk data to store'),
                new ParamTag('keys', ['array'], 'The list of fields to use to compute the unique identifier of an item'),
                new ParamTag('fields', ['array'], 'The list of updatable fields to take into account'),
                new ParamTag('progressToken', ['string'], 'The optional progress token to update job progress'),
                new ParamTag('commonData', ['array'], 'The optional common data to use for each item'),
                new ParamTag('options', ['array'], 'Options'),
                new ReturnTag([$model ?: 'array']),
                new ThrowsTag(['\\Exception'], 'if an error occured'),
            ]
        ));
        $zMethod->setParameters([
            new ParameterGenerator('bulkData', 'array', []),
            new ParameterGenerator('keys', 'array', []),
            new ParameterGenerator('fields', 'array', []),
            new ParameterGenerator('commonData', 'array', []),
            new ParameterGenerator('progressToken', 'string', new ValueGenerator(null)),
            new ParameterGenerator('options', 'array', []),
        ]);

        $returnBody = sprintf('$this->getSdk()->create(\'%s\', [\'settings\' => [\'keys\' => array_merge(array_keys($commonData), $keys), \'fields\' => $fields, \'common\' => $commonData, \'progressToken\' => $progressToken], \'data\' => $bulkData], %s + $options + $this->options)', $definition['route'], str_replace("\n", '', var_export(['import' => true] + (isset($definition['options']) ? $definition['options'] : []), true)));
        $zMethod->setBody(
            sprintf('return '.($model ? ('$this->modelize('.$returnBody.', $options + $this->options + [\'model\' => \''.$model.'\'])') : $returnBody)).';'
        );
    }
}
