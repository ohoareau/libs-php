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
class CreateCrudSdkCodeGenerator extends Base\AbstractCrudSdkCodeGenerator
{
    /**
     * @param MethodGenerator $zMethod
     * @param array           $definition
     *
     * @Annotation\CodeGeneratorMethodType("crud.create")
     */
    public function generateCrudCreateMethod(MethodGenerator $zMethod, $definition = [])
    {
        $model = isset($definition['model']) ? (is_bool($definition['model']) ? $definition['returnType']['type'] : ('array' === $definition['model'] ? ($definition['returnType']['type'].'[]') : $definition['model'])) : null;

        $zMethod->setDocBlock(new DocBlockGenerator(
            sprintf('Create a new %s', $definition['type']),
            null,
            [
                new ParamTag('data', ['array'], 'Data to store'),
                new ParamTag('options', ['array'], 'Options'),
                new ReturnTag([$model ?: 'array']),
                new ThrowsTag(['\\Exception'], 'if an error occured'),
            ]
        ));
        $zMethod->setParameters([
            new ParameterGenerator('data', 'array', []),
            new ParameterGenerator('options', 'array', []),
        ]);

        $returnBody = sprintf('$this->getSdk()->create(\'%s\', %s + $data, %s + $options + $this->options)', $definition['route'], str_replace("\n", '', var_export(isset($definition['data']) ? $definition['data'] : [], true)), str_replace("\n", '', var_export(isset($definition['options']) ? $definition['options'] : [], true)));
        $zMethod->setBody(
            sprintf('return '.($model ? ('$this->modelize('.$returnBody.', $options + $this->options + [\'model\' => \''.$model.'\'])') : $returnBody)).';'
        );
    }
    /**
     * @param MethodGenerator $zMethod
     * @param array           $definition
     *
     * @Annotation\CodeGeneratorMethodType("crud.createFor")
     */
    public function generateCrudCreateForMethod(MethodGenerator $zMethod, $definition = [])
    {
        $model = isset($definition['model']) ? (is_bool($definition['model']) ? $definition['returnType']['type'] : $definition['model']) : null;

        if (!is_array($definition['for'])) {
            $definition['for'] = [$definition['for']];
        }

        $forParamTags = [];
        $forParams    = [];
        $forData      = [];

        foreach ($definition['for'] as $for) {
            $forParamTags[] = new ParamTag($for, ['string'], sprintf('The %s for which to create the %s', $for, $definition['type']));
            $forParams[]    = new ParameterGenerator($for, 'string');
            $forData[] = "'$for' => \$$for";
        }
        $zMethod->setDocBlock(new DocBlockGenerator(
            sprintf('Create a new %s for specified the %s', $definition['type'], join(' and ', $definition['for'])),
            null,
            array_merge(
                $forParamTags,
                [
                    new ParamTag('data', ['array'], 'Data to store'),
                    new ParamTag('options', ['array'], 'Options'),
                    new ReturnTag([$model ?: 'array']),
                    new ThrowsTag(['\\Exception'], 'if an error occured'),
                ]
            )
        ));
        $zMethod->setParameters(
            array_merge(
                $forParams,
                [
                    new ParameterGenerator('data', 'array', []),
                    new ParameterGenerator('options', 'array', []),
                ]
            )
        );

        $returnBody = sprintf('$this->getSdk()->create(\'%s\', %s + ['.join(', ', $forData).'] + $data, %s + $options + $this->options)', $definition['route'], var_export(isset($definition['data']) ? $definition['data'] : [], true), var_export(isset($definition['options']) ? $definition['options'] : [], true));
        $zMethod->setBody(
            sprintf('return '.($model ? ('$this->modelize('.$returnBody.', $options + $this->options + [\'model\' => \''.$model.'\'])') : $returnBody)).';'
        );
    }
    /**
     * @param MethodGenerator $zMethod
     * @param array           $definition
     *
     * @Annotation\CodeGeneratorMethodType("crud.createWithUploadingPropertyFileFor")
     */
    public function generateCrudCreateWithUploadingPropertyFileForMethod(MethodGenerator $zMethod, $definition = [])
    {
        $model = isset($definition['model']) ? (is_bool($definition['model']) ? $definition['returnType']['type'] : $definition['model']) : null;

        $zMethod->setDocBlock(new DocBlockGenerator(
            sprintf('Import the specified %s %s file for the specified %s', $definition['type'], $definition['for'], $definition['property']),
            null,
            [
                new ParamTag($definition['for'], ['string'], sprintf('The %s for which to import the file', $definition['for'])),
                new ParamTag('file', ['\Symfony\Component\HttpFoundation\File\File'], sprintf('The %s %s file to import', $definition['for'], $definition['property'])),
                new ParamTag('data', ['array'], 'Extra information to send'),
                new ParamTag('options', ['array'], 'Options'),
                new ReturnTag([$model ?: 'array']),
                new ThrowsTag(['\\Exception'], 'if an error occured'),
            ]
        ));
        $zMethod->setParameters([
            new ParameterGenerator($definition['for'], 'string'),
            new ParameterGenerator('file', '\Symfony\Component\HttpFoundation\File\File'),
            new ParameterGenerator('data', 'array', []),
            new ParameterGenerator('options', 'array', []),
        ]);

        if (strpos($definition['property'], '.')) {
            $remainingPropertyTokens = explode('.', $definition['property']);
            $lastPropertyToken = array_pop($remainingPropertyTokens);
            $exportedRemainingPropertyTokens = str_replace("\n", '', var_export($remainingPropertyTokens, true));
            $propertyBody = <<<EOF
\$_data = &\$data;
foreach ($exportedRemainingPropertyTokens as \$propertyToken) {
    if (!isset(\$_data[\$propertyToken]) || !is_array(\$_data[\$propertyToken])) {
        \$_data[\$propertyToken] = [];
    }
    \$_data = &\$_data[\$propertyToken];
}
\$_data['$lastPropertyToken'] = \$encodedFileContent;
\$_data['{$lastPropertyToken}ContentType'] = \$file->getMimeType();
if (method_exists(\$file, 'getClientOriginalName')) {
    \$_data['{$lastPropertyToken}Name'] = \$file->getClientOriginalName();
}
EOF
            ;
        } else {
            $propertyBody = <<<EOF
\$data['{$definition['property']}'] = \$encodedFileContent;
\$data['{$definition['property']}ContentType'] = \$file->getMimeType();
if (method_exists(\$file, 'getClientOriginalName')) {
    \$data['{$definition['property']}Name'] = \$file->getClientOriginalName();
}
EOF
            ;
        }
        $forcedData = str_replace("\n", '', var_export(isset($definition['data']) ? $definition['data'] : [], true));
        $prefixBody = <<<EOF
\$forcedData = $forcedData;
\$encodedFileContent = base64_encode(file_get_contents(\$file->getRealPath()));
\$data = ['{$definition['for']}' => \${$definition['for']}] + \$forcedData + \$data;
$propertyBody

EOF
        ;
        $returnBody = '$this->getSdk()->create(\''.$definition['route'].'\', $data, $options + $this->options)';
        $zMethod->setBody(
            sprintf($prefixBody.'return '.($model ? ('$this->modelize('.$returnBody.', $options + $this->options + [\'model\' => \''.$model.'\'])') : $returnBody)).';'
        );
    }
}
