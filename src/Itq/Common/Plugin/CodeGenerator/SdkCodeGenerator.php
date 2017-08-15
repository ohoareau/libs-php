<?php

/*
 * This file is part of the COMMON package.
 *
 * (c) itiQiti SAS <opensource@itiqiti.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Itq\Common\Plugin\CodeGenerator;

use /** @noinspection PhpUnusedAliasInspection */ Itq\Common\Annotation;

use Zend\Code\Generator\ValueGenerator;
use Zend\Code\Generator\MethodGenerator;
use Zend\Code\Generator\DocBlockGenerator;
use Zend\Code\Generator\ParameterGenerator;
use Zend\Code\Generator\DocBlock\Tag\ParamTag;
use Zend\Code\Generator\DocBlock\Tag\ReturnTag;
use Zend\Code\Generator\DocBlock\Tag\ThrowsTag;

/**
 * @author itiQiti Dev Team <opensource@itiqiti.com>
 */
class SdkCodeGenerator extends Base\AbstractCodeGenerator
{
    /**
     * @param MethodGenerator $zMethod
     * @param array           $definition
     *
     * @Annotation\CodeGeneratorMethodType("basic")
     */
    public function generateBasicMethod(MethodGenerator $zMethod, array $definition = [])
    {
        if (isset($definition['body'])) {
            $zMethod->setBody($definition['body']);
        }
        if (isset($definition['parameters']) && is_array($definition['parameters'])) {
            foreach ($definition['parameters'] as $parameter) {
                $zMethod->setParameter($parameter);
            }
        }
        if (isset($definition['returnType'])) {
            $zMethod->setDocBlock(new DocBlockGenerator(
                $definition['name'],
                null,
                [
                    new ReturnTag($definition['returnType']),
                ]
            ));
        }
    }
    /**
     * @param MethodGenerator $zMethod
     * @param array           $definition
     *
     * @Annotation\CodeGeneratorMethodType("get")
     */
    public function generateGetMethod(MethodGenerator $zMethod, $definition = [])
    {
        unset($definition);

        $zMethod->setParameters([]);
    }
    /**
     * @param MethodGenerator $zMethod
     * @param array           $definition
     *
     * @Annotation\CodeGeneratorMethodType("result_converter")
     */
    public function generateResultConverterMethod(MethodGenerator $zMethod, $definition = [])
    {
        unset($definition);

        $zMethod->setParameters([]);

        $zMethod->setBody(<<<EOF
        if (isset(\$options['resultClass']) {
            if (!class_exists(\$options['resultClass'])) {
                throw new \RuntimeException(sprintf(\"Unable to convert result, class '%s' not found\", \$options['resultClass']), 500);
            }
            \$model = new \$options['resultClass'](\$result);
            if (method_exists(\$model, 'init')) {
                \$model->init(\$result);
            } else {
                \$hasGenericSetMethod = method_exists(\$model, 'set');
                foreach (\$result as \$k => \$v) {
                    \$method = 'set'.ucfirst(\$k);
                    if (method_exists(\$model, \$method)) {
                        \$model->\$method(\$v);
                    } elseif (property_exists(\$model, \$k)) {
                        \$model->\$k = \$v;
                    } elseif (\$hasGenericSetMethod) {
                        \$model->set(\$k, \$v);
                    }
                }
            }
        }

        return \$result;
EOF
        );
    }
    /**
     * @param MethodGenerator $zMethod
     * @param array           $definition
     *
     * @Annotation\CodeGeneratorMethodType("getStoredFilePath")
     */
    public function generateGetStoredFilePathMethod(MethodGenerator $zMethod, $definition = [])
    {
        $definition += ['field' => 'key'];
        $definition['route'] = str_replace(['{'.$definition['field'].'}'], '%s', $definition['route']);

        $zMethod->setDocBlock(new DocBlockGenerator(
            sprintf('Return an array containing path and meta data of a remotely stored file by its %s', $definition['field']),
            null,
            [
                new ParamTag($definition['field'], ['string'], sprintf(ucfirst($definition['field']).' to retrieve')),
                new ParamTag('cached', ['bool'], 'Cache the content locally'),
                new ParamTag('options', ['array'], 'Options'),
                new ReturnTag(['array']),
                new ThrowsTag(['\\Exception'], 'if an error occured'),
            ]
        ));
        $zMethod->setParameters([
            new ParameterGenerator($definition['field'], 'string'),
            new ParameterGenerator('cached', 'bool', true),
            new ParameterGenerator('options', 'array', []),
        ]);


        $zMethod->setBody(<<<CODE
\$tmpFile = \$this->getSdk()->getCacheDir() . '/'.md5(__FILE__).'/'.md5(__METHOD__).'/'.md5(\${$definition['field']});
\$that = \$this;
\$fetchFunction = function (\${$definition['field']}, \$tmpFile, \$options) use (\$that) {
    \$a_time = microtime(true);
    \$data  = \$that->getSdk()->get(sprintf('{$definition['route']}', \${$definition['field']}), [], \$options);
    \$b_time = microtime(true);
    if (!is_dir(dirname(\$tmpFile))) {
        mkdir(dirname(\$tmpFile), 0777, true);
    }
    if (!is_array(\$data) || !isset(\$data['content'])) {
        throw new \\RuntimeException("Unable to retrieve content of stored file", 500);
    }
    file_put_contents(\$tmpFile, base64_decode(\$data['content']));
    unset(\$data['content']);
    if (isset(\$data['cacheTtl']) && 0 <= \$data['cacheTtl']) {
        \$data['expireDate'] = (new \\DateTime(sprintf("+ %d seconds", \$data['cacheTtl'])))->format('c');
    }
    \$data['fetchDate'] = (new \\DateTime())->format('c');
    \$data['fetchDuration'] = \$b_time - \$a_time;
    file_put_contents(\$tmpFile.'.json', json_encode(\$data));
};
\$readFunction = function (\$tmpFile) {
    \$data = [];
    if (is_file(\$tmpFile.'.json')) {
        \$data = @json_decode(file_get_contents(\$tmpFile . '.json'), true);
    }
    if (!is_array(\$data)) {
        \$data = [];
    }
    // default local cache duration if 1 hour
    \$data += ['expireDate' => (new \\DateTime('+ 1 hour'))->format('c')];
    return \$data;
};
if (!\$cached || !file_exists(\$tmpFile)) {
    \$fetchFunction(\${$definition['field']}, \$tmpFile, \$options);
}
\$data = \$readFunction(\$tmpFile);
\$n = 0;
while (\$n < 3 && isset(\$data['expireDate']) && ((\$maxDate = new \\DateTime(\$data['expireDate'])) < new \\DateTime())) {
    @unlink(\$tmpFile);
    @unlink(\$tmpFile.'.json');
    \$fetchFunction(\${$definition['field']}, \$tmpFile, \$options);
    \$data = \$readFunction(\$tmpFile);
    \$n++;
}

return ['path' => \$tmpFile] + \$data;
CODE
        );
    }
    /**
     * @param MethodGenerator $zMethod
     * @param array           $definition
     *
     * @Annotation\CodeGeneratorMethodType("sdk.construct")
     */
    public function generateSdkConstructMethod(MethodGenerator $zMethod, array $definition = [])
    {
        $zMethod->setDocBlock(new DocBlockGenerator(
            sprintf('Construct a %s service', $definition['serviceName']),
            null,
            [
                new ParamTag('sdk', ['SdkInterface'], 'Underlying SDK'),
                new ParamTag('options', ['array'], 'Recurring options'),
            ]
        ));
        $zMethod->setParameters([
            new ParameterGenerator('sdk', 'SdkInterface'),
            new ParameterGenerator('options', 'mixed', []),
        ]);
        $zMethod->setBody('$this->setSdk($sdk)->options = is_array($options) ? $options : [];');
    }
    /**
     * @param MethodGenerator $zMethod
     * @param array           $definition
     *
     * @Annotation\CodeGeneratorMethodType("sdk.modelize")
     */
    public function generateSdkModelizeMethod(MethodGenerator $zMethod, array $definition = [])
    {
        unset($definition);

        $zMethod->setDocBlock(new DocBlockGenerator(
            'Modelize the raw data to the specified model class',
            null,
            [
                new ParamTag('data', ['mixed'], 'The data to modelize'),
                new ParamTag('options', ['array'], 'Recurring options'),
            ]
        ));
        $zMethod->setParameters([
            new ParameterGenerator('data', 'mixed'),
            new ParameterGenerator('options', 'array', []),
        ]);
        $zMethod->setBody(<<<EOF
\$class = \$options['model'];

if ('\\\\' === substr(\$class, 0, 1)) {
    \$class = substr(\$class, 1);
}

if ('[]' === substr(\$class, -2)) {
    \$class = substr(\$class, 0, -2);
    \$items = [];
    foreach (\$data as \$k => \$item) {
        \$items[\$k] = new \$class(\$item);
        unset(\$data[\$k]);
    }

    unset(\$data);

    return \$items;
}

return new \$class(\$data);
EOF
        );
    }
    /**
     * @param MethodGenerator $zMethod
     * @param array           $definition
     *
     * @Annotation\CodeGeneratorMethodType("sdk.service.test.testConstruct")
     */
    public function generateSdkServiceTestTestConstructMethod(MethodGenerator $zMethod, array $definition = [])
    {
        $zMethod->setDocBlock(new DocBlockGenerator(
            sprintf('Test constructor for service %s', $definition['serviceName']),
            null,
            []
        ));
        $zMethod->setParameters([]);
        $zMethod->setBody(
            '$this->sdk = $this->getMockBuilder(\'TombolaDirecte\\\\Bundle\\\\SdkBundle\\\\Sdk\')->disableOriginalConstructor()->setMethods([])->getMock();'."\n".sprintf('$this->assertNotNull(new %sService($this->sdk));', ucfirst($definition['serviceName']))
        );
    }
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
    /**
     * @param MethodGenerator $zMethod
     * @param array           $definition
     *
     * @Annotation\CodeGeneratorMethodType("crud.sub.getBy")
     */
    public function generateCrudSubGetByMethod(MethodGenerator $zMethod, $definition = [])
    {
        $definition += ['field' => 'id', 'subType' => 'subType'];
        $definition['route'] = str_replace(['{'.$definition['field'].'}', '{id}'], '%s', $definition['route']);

        $zMethod->setDocBlock(new DocBlockGenerator(
            sprintf('Return the specified %s %s by %s %s', $definition['type'], $definition['subType'], $definition['type'], $definition['field']),
            null,
            [
                new ParamTag($definition['field'], ['mixed'], sprintf('%s of the %s', ucfirst($definition['field']), $definition['type'])),
                new ParamTag('id', ['string'], 'The id'),
                new ParamTag('fields', ['array'], 'Data to store'),
                new ParamTag('options', ['array'], 'Options'),
                new ReturnTag(['array']),
                new ThrowsTag(['\\Exception'], 'if an error occured'),
            ]
        ));
        $zMethod->setParameters([
            new ParameterGenerator($definition['field'], 'mixed'),
            new ParameterGenerator('id', 'string'),
            new ParameterGenerator('fields', 'array', []),
            new ParameterGenerator('options', 'array', []),
        ]);
        $zMethod->setBody(
            sprintf('return $this->getSdk()->get(sprintf(\'%s\', $%s, $id), $fields, $options + $this->options);', $definition['route'], $definition['field'])
        );
    }
    /**
     * @param MethodGenerator $zMethod
     * @param array           $definition
     *
     * @Annotation\CodeGeneratorMethodType("crud.sub.get")
     */
    public function generateCrudSubGetMethod(MethodGenerator $zMethod, $definition = [])
    {
        $model = isset($definition['model']) ? (is_bool($definition['model']) ? $definition['returnType']['type'] : $definition['model']) : null;

        $definition += ['subType' => 'subType'];
        $definition['route'] = str_replace(['{parentId}', '{id}'], '%s', $definition['route']);

        $zMethod->setDocBlock(new DocBlockGenerator(
            sprintf('Return the specified %s %s by %s id', $definition['type'], $definition['subType'], $definition['type']),
            null,
            [
                new ParamTag('parentId', ['string'], 'The parent id'),
                new ParamTag('id', ['mixed'], sprintf('ID of the %s', $definition['type'])),
                new ParamTag('fields', ['array'], 'List of fields to retrieve'),
                new ParamTag('options', ['array'], 'Options'),
                new ReturnTag([$model ?: 'array']),
                new ThrowsTag(['\\Exception'], 'if an error occured'),
            ]
        ));
        $zMethod->setParameters([
            new ParameterGenerator('parentId', 'string'),
            new ParameterGenerator('id', 'string'),
            new ParameterGenerator('fields', 'array', []),
            new ParameterGenerator('options', 'array', []),
        ]);

        $returnBody = sprintf('$this->getSdk()->get(sprintf(\'%s\', $parentId, $id), $fields, $options + $this->options)', $definition['route'], isset($definition['route']) ? $definition['route'] : null);

        $zMethod->setBody(
            'return '.($model ? ('$this->modelize('.$returnBody.', $options + $this->options + [\'model\' => \''.$model.'\'])') : $returnBody).';'
        );
    }
    /**
     * @param MethodGenerator $zMethod
     * @param array           $definition
     *
     * @Annotation\CodeGeneratorMethodType("crud.sub.getPropertyBy")
     */
    public function generateCrudSubGetPropertyByMethod(MethodGenerator $zMethod, $definition = [])
    {
        $definition += ['field' => 'id', 'subType' => 'subType', 'property' => 'id'];
        $definition['route'] = str_replace(['{'.$definition['field'].'}', '{id}', '{'.$definition['property'].'}'], '%s', $definition['route']);

        $zMethod->setDocBlock(new DocBlockGenerator(
            sprintf('Return the specified %s %s %s by %s', $definition['type'], $definition['subType'], $definition['property'], $definition['field']),
            null,
            [
                new ParamTag($definition['field'], ['mixed'], sprintf(ucfirst($definition['field']).' of the %s %s', $definition['type'], $definition['subType'])),
                new ParamTag('id', ['string'], 'The id'),
                new ParamTag('options', ['array'], 'Options'),
                new ReturnTag(['mixed']),
                new ThrowsTag(['\\Exception'], 'if an error occured'),
            ]
        ));
        $zMethod->setParameters([
            new ParameterGenerator($definition['field'], 'string'),
            new ParameterGenerator('id', 'string'),
            new ParameterGenerator('options', 'array', []),
        ]);
        $zMethod->setBody(
            sprintf('return $this->getSdk()->get(sprintf(\'%s\', $%s, $id), [], $options + $this->options);', $definition['route'], $definition['field'])
        );
    }
    /**
     * @param MethodGenerator $zMethod
     * @param array           $definition
     *
     * @Annotation\CodeGeneratorMethodType("crud.sub.getPropertyPathBy")
     */
    public function generateCrudSubGetPropertyPathByMethod(MethodGenerator $zMethod, $definition = [])
    {
        $definition += ['field' => 'id', 'subType' => 'subType', 'property' => 'id'];
        $definition['route'] = str_replace(['{'.$definition['field'].'}', '{'.$definition['property'].'}'], '%s', $definition['route']);

        $zMethod->setDocBlock(new DocBlockGenerator(
            sprintf('Return the local path of the file containing the specified %s %s %s by %s', $definition['type'], $definition['subType'], $definition['property'], $definition['field']),
            null,
            [
                new ParamTag($definition['field'], ['mixed'], sprintf(ucfirst($definition['field']).' of the %s %s', $definition['type'], $definition['subType'])),
                new ParamTag('id', ['string'], 'The id'),
                new ParamTag('options', ['array'], 'Options'),
                new ReturnTag(['mixed']),
                new ThrowsTag(['\\Exception'], 'if an error occured'),
            ]
        ));
        $zMethod->setParameters([
            new ParameterGenerator($definition['field'], 'string'),
            new ParameterGenerator('id', 'string'),
            new ParameterGenerator('options', 'array', []),
        ]);
        $zMethod->setBody(
            sprintf('return $this->getSdk()->getPath(sprintf(\'%s\', $%s, $id), [], [\'format\' => \'%s\'] + $options + $this->options);', $definition['route'], $definition['field'], $definition['format'])
        );
    }
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
     * @Annotation\CodeGeneratorMethodType("crud.find")
     */
    public function generateCrudFindMethod(MethodGenerator $zMethod, $definition = [])
    {
        $model = isset($definition['model']) ? (is_bool($definition['model']) ? $definition['returnType']['type'] : $definition['model']) : null;

        $zMethod->setDocBlock(new DocBlockGenerator(
            sprintf('Find %ss', $definition['type']),
            null,
            [
                new ParamTag('criteria', ['array'], sprintf('Optional criteria to filter %ss', $definition['type'])),
                new ParamTag('fields', ['array'], 'Optional fields to retrieve'),
                new ParamTag('limit', ['int'], 'Optional limit'),
                new ParamTag('offset', ['int'], 'Optional offset'),
                new ParamTag('sorts', ['array'], 'Optional sorts'),
                new ParamTag('options', ['array'], 'Options'),
                new ReturnTag([$model ?: 'array']),
                new ThrowsTag(['\\Exception'], 'if an error occured'),
            ]
        ));
        $zMethod->setParameters([
            new ParameterGenerator('criteria', 'array', []),
            new ParameterGenerator('fields', 'array', []),
            new ParameterGenerator('limit', 'int', new ValueGenerator(null)),
            new ParameterGenerator('offset', 'int', 0),
            new ParameterGenerator('sorts', 'array', []),
            new ParameterGenerator('options', 'array', []),
        ]);

        $returnBody = sprintf('$this->getSdk()->find(\'%s\', %s + $criteria, $fields, $limit, $offset, $sorts, $options + $this->options)', $definition['route'], str_replace("\n", '', var_export(isset($definition['criteria']) ? $definition['criteria'] : [], true)));

        $zMethod->setBody(
            'return '.($model ? ('$this->modelize('.$returnBody.', $options + $this->options + [\'model\' => \''.$model.'\'])') : $returnBody).';'
        );
    }
    /**
     * @param MethodGenerator $zMethod
     * @param array           $definition
     *
     * @Annotation\CodeGeneratorMethodType("crud.findFor")
     */
    public function generateCrudFindForMethod(MethodGenerator $zMethod, $definition = [])
    {
        $model = isset($definition['model']) ? (is_bool($definition['model']) ? $definition['returnType']['type'] : $definition['model']) : null;

        if (false !== strpos($definition['for'], '.')) {
            $definition['forField'] = substr($definition['for'], strpos($definition['for'], '.') + 1);
            $definition['for'] = substr($definition['for'], 0, strpos($definition['for'], '.'));
        }

        $zMethod->setDocBlock(new DocBlockGenerator(
            sprintf('Find %ss for the specified %s', $definition['type'], $definition['for'].(isset($definition['forField']) ? (' ('.$definition['forField'].')') : '')),
            null,
            [
                new ParamTag($definition['for'], ['string'], sprintf('%s in which to search for %ss', ucfirst($definition['for']).(isset($definition['forField']) ? (' ('.$definition['forField'].')') : ''), $definition['type'])),
                new ParamTag('criteria', ['array'], sprintf('Optional criteria to filter %ss', $definition['type'])),
                new ParamTag('fields', ['array'], 'Optional fields to retrieve'),
                new ParamTag('limit', ['int'], 'Optional limit'),
                new ParamTag('offset', ['int'], 'Optional offset'),
                new ParamTag('sorts', ['array'], 'Optional sorts'),
                new ParamTag('options', ['array'], 'Options'),
                new ReturnTag([$model ?: 'array']),
                new ThrowsTag(['\\Exception'], 'if an error occured'),
            ]
        ));
        $zMethod->setParameters([
            new ParameterGenerator($definition['for'], 'string'),
            new ParameterGenerator('criteria', 'array', []),
            new ParameterGenerator('fields', 'array', []),
            new ParameterGenerator('limit', 'int', new ValueGenerator(null)),
            new ParameterGenerator('offset', 'int', 0),
            new ParameterGenerator('sorts', 'array', []),
            new ParameterGenerator('options', 'array', []),
        ]);

        $returnBody = sprintf('$this->getSdk()->find(\'%s\', [\'%s\' => $%s] + $criteria, $fields, $limit, $offset, $sorts, $options + $this->options)', $definition['route'], $definition['for'].(isset($definition['forField']) ? ('.'.$definition['forField']) : ''), $definition['for']);

        $zMethod->setBody(
            'return '.($model ? ('$this->modelize('.$returnBody.', $options + $this->options + [\'model\' => \''.$model.'\'])') : $returnBody).';'
        );
    }
    /**
     * @param MethodGenerator $zMethod
     * @param array           $definition
     *
     * @Annotation\CodeGeneratorMethodType("crud.downloadFind")
     */
    public function generateCrudDownloadFindMethod(MethodGenerator $zMethod, $definition = [])
    {
        $zMethod->setDocBlock(new DocBlockGenerator(
            sprintf('Download found %ss', $definition['type']),
            null,
            [
                new ParamTag('format', ['string'], 'Format of the download'),
                new ParamTag('criteria', ['array'], sprintf('Optional criteria to filter %ss', $definition['type'])),
                new ParamTag('fields', ['array'], 'Optional fields to retrieve'),
                new ParamTag('limit', ['int'], 'Optional limit'),
                new ParamTag('offset', ['int'], 'Optional offset'),
                new ParamTag('sorts', ['array'], 'Optional sorts'),
                new ParamTag('options', ['array'], 'Options'),
                new ReturnTag(['array']),
                new ThrowsTag(['\\Exception'], 'if an error occured'),
            ]
        ));
        $zMethod->setParameters([
            new ParameterGenerator('format', 'string', 'xlsx'),
            new ParameterGenerator('criteria', 'array', []),
            new ParameterGenerator('fields', 'array', []),
            new ParameterGenerator('limit', 'int', new ValueGenerator(null)),
            new ParameterGenerator('offset', 'int', 0),
            new ParameterGenerator('sorts', 'array', []),
            new ParameterGenerator('options', 'array', []),
        ]);
        $zMethod->setBody(
            sprintf('return $this->getSdk()->find(\'%s\', $criteria, $fields, $limit, $offset, $sorts, [\'format\' => $format] + $options + $this->options);', $definition['route'])
        );
    }
    /**
     * @param MethodGenerator $zMethod
     * @param array           $definition
     *
     * @Annotation\CodeGeneratorMethodType("crud.sub.findBy")
     */
    public function generateCrudSubFindByMethod(MethodGenerator $zMethod, $definition = [])
    {
        $definition['route'] = str_replace('{'.$definition['field'].'}', '%s', $definition['route']);

        $zMethod->setDocBlock(new DocBlockGenerator(
            sprintf('Find %s %ss by %s', $definition['type'], $definition['subType'], $definition['field']),
            null,
            [
                new ParamTag($definition['field'], ['mixed'], sprintf(ucfirst($definition['field']).' of the %s', $definition['type'])),
                new ParamTag('criteria', ['array'], sprintf('Optional criteria to filter %ss', $definition['type'])),
                new ParamTag('fields', ['array'], 'Optional fields to retrieve'),
                new ParamTag('limit', ['int'], 'Optional limit'),
                new ParamTag('offset', ['int'], 'Optional offset'),
                new ParamTag('sorts', ['array'], 'Optional sorts'),
                new ParamTag('options', ['array'], 'Options'),
                new ReturnTag(['array']),
                new ThrowsTag(['\\Exception'], 'if an error occured'),
            ]
        ));
        $zMethod->setParameters([
            new ParameterGenerator($definition['field'], 'mixed'),
            new ParameterGenerator('criteria', 'array', []),
            new ParameterGenerator('fields', 'array', []),
            new ParameterGenerator('limit', 'int', new ValueGenerator(null)),
            new ParameterGenerator('offset', 'int', 0),
            new ParameterGenerator('sorts', 'array', []),
            new ParameterGenerator('options', 'array', []),
        ]);
        $zMethod->setBody(
            sprintf('return $this->getSdk()->find(sprintf(\'%s\', $%s), $criteria, $fields, $limit, $offset, $sorts, $options + $this->options);', $definition['route'], $definition['field'])
        );
    }
    /**
     * @param MethodGenerator $zMethod
     * @param array           $definition
     *
     * @Annotation\CodeGeneratorMethodType("crud.sub.find")
     */
    public function generateCrudSubFindMethod(MethodGenerator $zMethod, $definition = [])
    {
        $model = isset($definition['model']) ? (is_bool($definition['model']) ? $definition['returnType']['type'] : $definition['model']) : null;

        $definition['route'] = str_replace('{parentId}', '%s', $definition['route']);

        $zMethod->setDocBlock(new DocBlockGenerator(
            sprintf('Find %s %ss by %s id', $definition['type'], $definition['subType'], $definition['type']),
            null,
            [
                new ParamTag('parentId', ['string'], sprintf('Id of the %s', $definition['type'])),
                new ParamTag('criteria', ['array'], sprintf('Optional criteria to filter %ss', $definition['type'])),
                new ParamTag('fields', ['array'], 'Optional fields to retrieve'),
                new ParamTag('limit', ['int'], 'Optional limit'),
                new ParamTag('offset', ['int'], 'Optional offset'),
                new ParamTag('sorts', ['array'], 'Optional sorts'),
                new ParamTag('options', ['array'], 'Options'),
                new ReturnTag([$model ?: 'array']),
                new ThrowsTag(['\\Exception'], 'if an error occured'),
            ]
        ));
        $zMethod->setParameters([
            new ParameterGenerator('parentId', 'string'),
            new ParameterGenerator('criteria', 'array', []),
            new ParameterGenerator('fields', 'array', []),
            new ParameterGenerator('limit', 'int', new ValueGenerator(null)),
            new ParameterGenerator('offset', 'int', 0),
            new ParameterGenerator('sorts', 'array', []),
            new ParameterGenerator('options', 'array', []),
        ]);

        $returnBody = sprintf('$this->getSdk()->find(sprintf(\'%s\', $parentId), %s + $criteria, $fields, $limit, $offset, $sorts, $options + $this->options)', $definition['route'], str_replace("\n", '', var_export(isset($definition['criteria']) ? $definition['criteria'] : [], true)));

        $zMethod->setBody(
            'return '.($model ? ('$this->modelize('.$returnBody.', $options + $this->options + [\'model\' => \''.$model.'\'])') : $returnBody).';'
        );
    }
    /**
     * @param MethodGenerator $zMethod
     * @param array           $definition
     *
     * @Annotation\CodeGeneratorMethodType("crud.findPage")
     */
    public function generateCrudFindPageMethod(MethodGenerator $zMethod, $definition = [])
    {
        $model = isset($definition['model']) ? (is_bool($definition['model']) ? $definition['returnType']['type'] : $definition['model']) : null;
        $pageModel = '\\TombolaDirecte\\Bundle\\SdkBundle\\Model\\Response\\Util\\Page';

        $zMethod->setDocBlock(new DocBlockGenerator(
            sprintf('Find a page of %ss', $definition['type']),
            null,
            [
                new ParamTag('criteria', ['array'], sprintf('Optional criteria to filter %ss', $definition['type'])),
                new ParamTag('fields', ['array'], 'Optional fields to retrieve'),
                new ParamTag('page', ['int'], 'Rank of the page to retrieve'),
                new ParamTag('size', ['int'], 'Size of pages'),
                new ParamTag('sorts', ['array'], 'Optional sorts'),
                new ParamTag('options', ['array'], 'Options'),
                new ReturnTag([$model ? $pageModel : 'array']),
                new ThrowsTag(['\\Exception'], 'if an error occured'),
            ]
        ));
        $zMethod->setParameters([
            new ParameterGenerator('criteria', 'array', []),
            new ParameterGenerator('fields', 'array', []),
            new ParameterGenerator('page', 'int', 0),
            new ParameterGenerator('size', 'int', 10),
            new ParameterGenerator('sorts', 'array', []),
            new ParameterGenerator('options', 'array', []),
        ]);

        $returnBody = sprintf('$this->getSdk()->findPage(\'%s\', %s + $criteria, $fields, $page, $size, $sorts, $options + $this->options)', $definition['route'], str_replace("\n", '', var_export(isset($definition['criteria']) ? $definition['criteria'] : [], true)));

        if ($model) {
            $zMethod->setBody(<<<EOF
list(\$page, \$extra) = $returnBody;
unset(\$extra);
\$page['items'] = \$this->modelize(\$page['items'], \$options + \$this->options + ['model' => '$model']);

return \$this->modelize(\$page, ['model' => '$pageModel']);
EOF
            );
        } else {
            $zMethod->setBody('return '.$returnBody.';');
        }
    }
    /**
     * @param MethodGenerator $zMethod
     * @param array           $definition
     *
     * @Annotation\CodeGeneratorMethodType("crud.sub.findPage")
     */
    public function generateCrudSubFindPageMethod(MethodGenerator $zMethod, $definition = [])
    {
        $model = isset($definition['model']) ? (is_bool($definition['model']) ? $definition['returnType']['type'] : $definition['model']) : null;
        $pageModel = '\\TombolaDirecte\\Bundle\\SdkBundle\\Model\\Response\\Util\\Page';

        $definition['route'] = str_replace('{parentId}', '%s', $definition['route']);

        $zMethod->setDocBlock(new DocBlockGenerator(
            sprintf('Find a page of %s %ss by %s id', $definition['type'], $definition['subType'], $definition['type']),
            null,
            [
                new ParamTag('parentId', ['string'], sprintf('Id of the %s', $definition['type'])),
                new ParamTag('criteria', ['array'], sprintf('Optional criteria to filter %ss', $definition['subType'])),
                new ParamTag('fields', ['array'], 'Optional fields to retrieve'),
                new ParamTag('page', ['int'], 'Rank of the page to retrieve'),
                new ParamTag('size', ['int'], 'Size of pages'),
                new ParamTag('sorts', ['array'], 'Optional sorts'),
                new ParamTag('options', ['array'], 'Options'),
                new ReturnTag([$model ? $pageModel : 'array']),
                new ThrowsTag(['\\Exception'], 'if an error occured'),
            ]
        ));
        $zMethod->setParameters([
            new ParameterGenerator('parentId', 'string'),
            new ParameterGenerator('criteria', 'array', []),
            new ParameterGenerator('fields', 'array', []),
            new ParameterGenerator('page', 'int', 0),
            new ParameterGenerator('size', 'int', 10),
            new ParameterGenerator('sorts', 'array', []),
            new ParameterGenerator('options', 'array', []),
        ]);

        $returnBody = sprintf('$this->getSdk()->findPage(sprintf(\'%s\', $parentId), %s + $criteria, $fields, $page, $size, $sorts, $options + $this->options)', $definition['route'], str_replace("\n", '', var_export(isset($definition['criteria']) ? $definition['criteria'] : [], true)));

        if ($model) {
            $zMethod->setBody(<<<EOF
list(\$page, \$extra) = $returnBody;
unset(\$extra);
\$page['items'] = \$this->modelize(\$page['items'], \$options + \$this->options + ['model' => '$model']);

return \$this->modelize(\$page, ['model' => '$pageModel']);
EOF
            );
        } else {
            $zMethod->setBody('return '.$returnBody.';');
        }
    }
    /**
     * @param MethodGenerator $zMethod
     * @param array           $definition
     *
     * @Annotation\CodeGeneratorMethodType("crud.findPageFor")
     */
    public function generateCrudFindPageForMethod(MethodGenerator $zMethod, $definition = [])
    {
        $model = isset($definition['model']) ? (is_bool($definition['model']) ? $definition['returnType']['type'] : $definition['model']) : null;
        $pageModel = '\\TombolaDirecte\\Bundle\\SdkBundle\\Model\\Response\\Util\\Page';

        if (false !== strpos($definition['for'], '.')) {
            $definition['forField'] = substr($definition['for'], strpos($definition['for'], '.') + 1);
            $definition['for'] = substr($definition['for'], 0, strpos($definition['for'], '.'));
        }

        $zMethod->setDocBlock(new DocBlockGenerator(
            sprintf('Find a page of %ss for the specified %s', $definition['type'], $definition['for'].(isset($definition['forField']) ? (' ('.$definition['forField'].')') : '')),
            null,
            [
                new ParamTag($definition['for'], ['string'], sprintf('%s in which to search for %ss', ucfirst($definition['for']).(isset($definition['forField']) ? (' ('.$definition['forField'].')') : ''), $definition['type'])),
                new ParamTag('criteria', ['array'], sprintf('Optional criteria to filter %ss', $definition['type'])),
                new ParamTag('fields', ['array'], 'Optional fields to retrieve'),
                new ParamTag('page', ['int'], 'Rank of the page to retrieve'),
                new ParamTag('size', ['int'], 'Size of pages'),
                new ParamTag('sorts', ['array'], 'Optional sorts'),
                new ParamTag('options', ['array'], 'Options'),
                new ReturnTag([$model ? $pageModel : 'array']),
                new ThrowsTag(['\\Exception'], 'if an error occured'),
            ]
        ));
        $zMethod->setParameters([
            new ParameterGenerator($definition['for'], 'string'),
            new ParameterGenerator('criteria', 'array', []),
            new ParameterGenerator('fields', 'array', []),
            new ParameterGenerator('page', 'int', 0),
            new ParameterGenerator('size', 'int', 10),
            new ParameterGenerator('sorts', 'array', []),
            new ParameterGenerator('options', 'array', []),
        ]);

        $returnBody = sprintf('$this->getSdk()->findPage(\'%s\', [\'%s\' => $%s] + $criteria, $fields, $page, $size, $sorts, $options + $this->options)', $definition['route'], $definition['for'].(isset($definition['forField']) ? ('.'.$definition['forField']) : ''), $definition['for']);

        if ($model) {
            $zMethod->setBody(<<<EOF
list(\$page, \$extra) = $returnBody;
unset(\$extra);
\$page['items'] = \$this->modelize(\$page['items'], \$options + \$this->options + ['model' => '$model']);

return \$this->modelize(\$page, ['model' => '$pageModel']);
EOF
            );
        } else {
            $zMethod->setBody('return '.$returnBody.';');
        }
    }
    /**
     * @param MethodGenerator $zMethod
     * @param array           $definition
     *
     * @Annotation\CodeGeneratorMethodType("crud.streamContentBy")
     */
    public function generateCrudStreamContentByMethod(MethodGenerator $zMethod, $definition = [])
    {
        $definition += ['field' => 'id', 'property' => 'id'];
        $definition['route'] = str_replace(['{'.$definition['field'].'}', '{'.$definition['property'].'}'], '%s', $definition['route']);

        $zMethod->setDocBlock(new DocBlockGenerator(
            sprintf('Return a Symfony Streamed Response of the %s specified by %s', $definition['type'], $definition['field']),
            null,
            [
                new ParamTag($definition['field'], ['mixed'], sprintf(ucfirst($definition['field']).' of the %s', $definition['type'])),
                new ParamTag('options', ['array'], 'Options'),
                new ReturnTag(['\Symfony\Component\HttpFoundation\Response']),
                new ThrowsTag(['\\Exception'], 'if an error occured'),
            ]
        ));
        $zMethod->setParameters([
            new ParameterGenerator($definition['field'], 'string'),
            new ParameterGenerator('options', 'array', []),
        ]);


        $code = <<<CODE
\$data  = \$this->getSdk()->get(sprintf('%s', $%s), ['token', 'fingerPrint', 'content', 'contentType'], \$options);
return new \Symfony\Component\HttpFoundation\Response(base64_decode(\$data['content']), 200, ['Content-Type' => \$data['contentType']]);
CODE;

        $zMethod->setBody(
            sprintf($code, $definition['route'], $definition['field'])
        );
    }
    /**
     * @param MethodGenerator $zMethod
     * @param array           $definition
     *
     * @Annotation\CodeGeneratorMethodType("crud.streamByWithSecurityTokenAndFingerPrint")
     */
    public function generateCrudStreamByWithSecurityTokenAndFingerPrintMethod(MethodGenerator $zMethod, $definition = [])
    {
        $definition += ['field' => 'id', 'securityField' => 'securityToken', 'format' => null, 'property' => 'content', 'fingerPrintField' => 'fingerPrint'];
        $definition['route'] = str_replace(['{'.$definition['field'].'}', '{'.$definition['securityField'].'}'], '%s', $definition['route']);

        $zMethod->setDocBlock(new DocBlockGenerator(
            sprintf('Return a Symfony Binary File Response of the %s of a %s specified by its %s and %s', $definition['property'], $definition['type'], $definition['field'], $definition['securityField']),
            null,
            [
                new ParamTag($definition['field'], ['string'], sprintf(ucfirst($definition['field']).' of the %s', $definition['type'])),
                new ParamTag($definition['securityField'], ['string'], sprintf(ucfirst($definition['securityField']).' of the %s', $definition['type'])),
                new ParamTag('fingerPrint', ['string'], sprintf('finger print of the %s of the %s', $definition['property'], $definition['type'])),
                new ParamTag('fileName', ['string'], 'The file name to send to the browser'),
                new ParamTag('attachment', ['bool'], 'Send as attachment or inline'),
                new ParamTag('cached', ['bool'], 'Cache the content locally'),
                new ParamTag('options', ['array'], 'Options'),
                new ReturnTag(['\Symfony\Component\HttpFoundation\BinaryFileResponse']),
                new ThrowsTag(['\\Exception'], 'if an error occured'),
            ]
        ));
        $zMethod->setParameters([
            new ParameterGenerator($definition['field'], 'string'),
            new ParameterGenerator($definition['securityField'], 'string'),
            new ParameterGenerator('fingerPrint', 'string'),
            new ParameterGenerator('fileName', 'string'),
            new ParameterGenerator('attachment', 'bool', false),
            new ParameterGenerator('cached', 'bool', true),
            new ParameterGenerator('options', 'array', []),
        ]);


        $code = <<<CODE
\$tmpFile = \$this->getSdk()->getCacheDir() . '/'.md5(__FILE__).'/'.md5(__METHOD__).'/'.md5(\${$definition['field']}.'-'.\${$definition['securityField']}.'-'.\$fingerPrint);
if (!\$cached || !file_exists(\$tmpFile)) {
    // the fingerPrint is ignored.
    \$data  = \$this->getSdk()->get(sprintf('{$definition['route']}', \${$definition['field']}, \${$definition['securityField']}), ['{$definition['property']}', '{$definition['contentTypeField']}', '{$definition['fingerPrintField']}'], \$options);
    if (!is_dir(dirname(\$tmpFile))) {
        mkdir(dirname(\$tmpFile), 0777, true);
    }
    file_put_contents(\$tmpFile, base64_decode(\$data['{$definition['property']}']));
    file_put_contents(\$tmpFile.'.format', \$data['{$definition['contentTypeField']}']);
}
\$response = new \Symfony\Component\HttpFoundation\BinaryFileResponse(new \SplFileInfo(\$tmpFile));
\$response->headers->set('Content-Type', file_get_contents(\$tmpFile.'.format'));
\$response->setContentDisposition(
    \$attachment ? \Symfony\Component\HttpFoundation\ResponseHeaderBag::DISPOSITION_ATTACHMENT : \Symfony\Component\HttpFoundation\ResponseHeaderBag::DISPOSITION_INLINE,
    \$fileName ? \$fileName : null
);
return \$response;
CODE;

        $zMethod->setBody($code);
    }
    /**
     * @param MethodGenerator $zMethod
     * @param array           $definition
     *
     * @Annotation\CodeGeneratorMethodType("crud.streamByWithFingerPrint")
     */
    public function generateCrudStreamByWithFingerPrintMethod(MethodGenerator $zMethod, $definition = [])
    {
        $definition += ['decode' => true, 'field' => 'id', 'format' => null, 'property' => 'content', 'fingerPrintField' => 'fingerPrint'];
        $definition['route'] = str_replace(['{'.$definition['field'].'}'], '%s', $definition['route']);

        $zMethod->setDocBlock(new DocBlockGenerator(
            sprintf('Return a Symfony Binary File Response of the %s of a %s specified by its %s', $definition['property'], $definition['type'], $definition['field']),
            null,
            [
                new ParamTag($definition['field'], ['string'], sprintf(ucfirst($definition['field']).' of the %s', $definition['type'])),
                new ParamTag('fingerPrint', ['string'], sprintf('finger print of the %s of the %s', $definition['property'], $definition['type'])),
                new ParamTag('fileName', ['string'], 'The file name to send to the browser'),
                new ParamTag('attachment', ['bool'], 'Send as attachment or inline'),
                new ParamTag('cached', ['bool'], 'Cache the content locally'),
                new ParamTag('options', ['array'], 'Options'),
                new ReturnTag(['\Symfony\Component\HttpFoundation\BinaryFileResponse']),
                new ThrowsTag(['\\Exception'], 'if an error occured'),
            ]
        ));
        $zMethod->setParameters([
            new ParameterGenerator($definition['field'], 'string'),
            new ParameterGenerator('fingerPrint', 'string'),
            new ParameterGenerator('fileName', 'string'),
            new ParameterGenerator('attachment', 'bool', false),
            new ParameterGenerator('cached', 'bool', true),
            new ParameterGenerator('options', 'array', []),
        ]);


        $contentTypeValue = isset($definition['contentTypeField']) ? ('$data[\''.$definition['contentTypeField'].'\']') : (isset($definition['contentType']) ? ("'".$definition['contentType']."'") : "'application/octet-stream'");
        if (isset($definition['contentTypeField'])) {
            $fetchedFields = "['{$definition['property']}', '{$definition['contentTypeField']}']";
        } else {
            $fetchedFields = "['{$definition['property']}']";
        }
        $decoded = $definition['decode'] ? 'base64_decode' : '';
        $code = <<<CODE
\$tmpFile = \$this->getSdk()->getCacheDir() . '/'.md5(__FILE__).'/'.md5(__METHOD__).'/'.md5(\${$definition['field']}.'-'.\$fingerPrint);
if (!\$cached || !file_exists(\$tmpFile)) {
    // the fingerPrint is ignored.
    \$data  = \$this->getSdk()->get(sprintf('{$definition['route']}', \${$definition['field']}), $fetchedFields, \$options);
    if (!is_dir(dirname(\$tmpFile))) {
        mkdir(dirname(\$tmpFile), 0777, true);
    }
    file_put_contents(\$tmpFile, $decoded(\$data['{$definition['property']}']));
    file_put_contents(\$tmpFile.'.format', $contentTypeValue);
}
\$response = new \Symfony\Component\HttpFoundation\BinaryFileResponse(new \SplFileInfo(\$tmpFile));
\$response->headers->set('Content-Type', file_get_contents(\$tmpFile.'.format'));
\$response->setContentDisposition(
    \$attachment ? \Symfony\Component\HttpFoundation\ResponseHeaderBag::DISPOSITION_ATTACHMENT : \Symfony\Component\HttpFoundation\ResponseHeaderBag::DISPOSITION_INLINE,
    \$fileName ? \$fileName : null
);
return \$response;
CODE;

        $zMethod->setBody($code);
    }
    /**
     * @param MethodGenerator $zMethod
     * @param array           $definition
     *
     * @Annotation\CodeGeneratorMethodType("crud.streamPropertyFieldWithFingerPrint")
     */
    public function generateCrudStreamPropertyFieldWithFingerPrintMethod(MethodGenerator $zMethod, $definition = [])
    {
        $definition += ['field' => 'content', 'format' => null, 'property' => null, 'fingerPrintField' => 'fingerPrint'];
        $definition['route'] = str_replace(['{id}'], '%s', $definition['route']);

        $zMethod->setDocBlock(new DocBlockGenerator(
            sprintf('Return a Symfony Binary File Response of the %s of the %s of a %s specified by %s id', $definition['field'], $definition['property'], $definition['type'], $definition['type']),
            null,
            [
                new ParamTag('id', ['string'], sprintf('ID of the %s', $definition['type'])),
                new ParamTag('fingerPrint', ['string'], sprintf('finger print of the %s %s %s', $definition['type'], $definition['property'], $definition['field'])),
                new ParamTag('fileName', ['string'], 'The file name to send to the browser'),
                new ParamTag('attachment', ['bool'], 'Send as attachment or inline'),
                new ParamTag('cached', ['bool'], 'Cache the content locally'),
                new ParamTag('options', ['array'], 'Options'),
                new ReturnTag(['\Symfony\Component\HttpFoundation\BinaryFileResponse']),
                new ThrowsTag(['\\Exception'], 'if an error occured'),
            ]
        ));
        $zMethod->setParameters([
            new ParameterGenerator('id', 'string'),
            new ParameterGenerator('fingerPrint', 'string'),
            new ParameterGenerator('fileName', 'string'),
            new ParameterGenerator('attachment', 'bool', false),
            new ParameterGenerator('cached', 'bool', true),
            new ParameterGenerator('options', 'array', []),
        ]);


        $code = <<<CODE
\$tmpFile = \$this->getSdk()->getCacheDir() . '/'.md5(__FILE__).'/'.md5(__METHOD__).'/'.md5(\$id.'-'.\$fingerPrint);
if (!\$cached || !file_exists(\$tmpFile)) {
    // the fingerPrint is ignored.
    \$data  = \$this->getSdk()->get(sprintf('{$definition['route']}', \$id), ['{$definition['property']}'], \$options);
    if (!is_dir(dirname(\$tmpFile))) {
        mkdir(dirname(\$tmpFile), 0777, true);
    }
    file_put_contents(\$tmpFile, base64_decode(\$data['{$definition['property']}']['{$definition['field']}']));
    file_put_contents(\$tmpFile.'.format', \$data['{$definition['property']}']['{$definition['contentTypeField']}']);
}
\$response = new \Symfony\Component\HttpFoundation\BinaryFileResponse(new \SplFileInfo(\$tmpFile));
\$response->headers->set('Content-Type', file_get_contents(\$tmpFile.'.format'));
\$response->setContentDisposition(
    \$attachment ? \Symfony\Component\HttpFoundation\ResponseHeaderBag::DISPOSITION_ATTACHMENT : \Symfony\Component\HttpFoundation\ResponseHeaderBag::DISPOSITION_INLINE,
    \$fileName ? \$fileName : null
);
return \$response;
CODE;

        $zMethod->setBody($code);
    }
    /**
     * @param MethodGenerator $zMethod
     * @param array           $definition
     *
     * @Annotation\CodeGeneratorMethodType("crud.streamPropertyWithFingerPrint")
     */
    public function generateCrudStreamPropertyWithFingerPrintMethod(MethodGenerator $zMethod, $definition = [])
    {
        $definition += ['decode' => true, 'routeVariable' => 'id', 'format' => null, 'property' => 'content', 'fingerPrintField' => 'fingerPrint', 'contentType' => null];
        $definition['route'] = str_replace([sprintf('{%s}', $definition['routeVariable'])], '%s', $definition['route']);

        $zMethod->setDocBlock(new DocBlockGenerator(
            sprintf('Return a Symfony Binary File Response of the %s of a %s specified by %s id', $definition['property'], $definition['type'], $definition['type']),
            null,
            [
                new ParamTag('id', ['string'], sprintf('ID of the %s', $definition['type'])),
                new ParamTag('fingerPrint', ['string'], sprintf('finger print of the %s %s', $definition['type'], $definition['property'])),
                new ParamTag('fileName', ['string'], 'The file name to send to the browser'),
                new ParamTag('attachment', ['bool'], 'Send as attachment or inline'),
                new ParamTag('cached', ['bool'], 'Cache the content locally'),
                new ParamTag('options', ['array'], 'Options'),
                new ReturnTag(['\Symfony\Component\HttpFoundation\BinaryFileResponse']),
                new ThrowsTag(['\\Exception'], 'if an error occured'),
            ]
        ));
        $zMethod->setParameters([
            new ParameterGenerator('id', 'string'),
            new ParameterGenerator('fingerPrint', 'string'),
            new ParameterGenerator('fileName', 'string'),
            new ParameterGenerator('attachment', 'bool', false),
            new ParameterGenerator('cached', 'bool', true),
            new ParameterGenerator('options', 'array', []),
        ]);


        $contentTypeValue = isset($definition['contentTypeField']) ? ('$data[\''.$definition['contentTypeField'].'\']') : (isset($definition['contentType']) ? ("'".$definition['contentType']."'") : "'application/octet-stream'");
        if (isset($definition['contentTypeField'])) {
            $fetchedFields = "['{$definition['property']}', '{$definition['contentTypeField']}']";
        } else {
            $fetchedFields = "['{$definition['property']}']";
        }
        $decoded = $definition['decode'] ? 'base64_decode' : '';

        $code = <<<CODE
\$tmpFile = \$this->getSdk()->getCacheDir() . '/'.md5(__FILE__).'/'.md5(__METHOD__).'/'.md5(\$id.'-'.\$fingerPrint);
if (!\$cached || !file_exists(\$tmpFile)) {
    // the fingerPrint is ignored.
    \$data  = \$this->getSdk()->get(sprintf('{$definition['route']}', \$id), $fetchedFields, \$options);
    if (!is_dir(dirname(\$tmpFile))) {
        mkdir(dirname(\$tmpFile), 0777, true);
    }
    file_put_contents(\$tmpFile, $decoded(\$data['{$definition['property']}']));
    file_put_contents(\$tmpFile.'.format', $contentTypeValue);
}
\$response = new \Symfony\Component\HttpFoundation\BinaryFileResponse(new \SplFileInfo(\$tmpFile));
\$response->headers->set('Content-Type', file_get_contents(\$tmpFile.'.format'));
\$response->setContentDisposition(
    \$attachment ? \Symfony\Component\HttpFoundation\ResponseHeaderBag::DISPOSITION_ATTACHMENT : \Symfony\Component\HttpFoundation\ResponseHeaderBag::DISPOSITION_INLINE,
    \$fileName ? \$fileName : null
);
return \$response;
CODE;

        $zMethod->setBody($code);
    }
    /**
     * @param MethodGenerator $zMethod
     * @param array           $definition
     *
     * @Annotation\CodeGeneratorMethodType("crud.sub.streamPropertyWithFingerPrint")
     */
    public function generateCrudSubStreamPropertyWithFingerPrintMethod(MethodGenerator $zMethod, $definition = [])
    {
        $definition += ['format' => null, 'property' => 'content', 'fingerPrintField' => 'fingerPrint'];
        $definition['route'] = str_replace(['{parentId}', '{id}'], '%s', $definition['route']);

        $zMethod->setDocBlock(new DocBlockGenerator(
            sprintf('Return a Symfony Binary File Response of the %s of a %s %s specified by %s id and %s id', $definition['property'], $definition['type'], $definition['subType'], $definition['type'], $definition['subType']),
            null,
            [
                new ParamTag('parentId', ['string'], sprintf('ID of the %s', $definition['type'])),
                new ParamTag('id', ['string'], sprintf('ID of the %s', $definition['subType'])),
                new ParamTag('fingerPrint', ['string'], sprintf('finger print of the %s %s %s', $definition['type'], $definition['subType'], $definition['property'])),
                new ParamTag('fileName', ['string'], 'The file name to send to the browser'),
                new ParamTag('attachment', ['bool'], 'Send as attachment or inline'),
                new ParamTag('cached', ['bool'], 'Cache the content locally'),
                new ParamTag('options', ['array'], 'Options'),
                new ReturnTag(['\Symfony\Component\HttpFoundation\BinaryFileResponse']),
                new ThrowsTag(['\\Exception'], 'if an error occured'),
            ]
        ));
        $zMethod->setParameters([
            new ParameterGenerator('parentId', 'string'),
            new ParameterGenerator('id', 'string'),
            new ParameterGenerator('fingerPrint', 'string'),
            new ParameterGenerator('fileName', 'string'),
            new ParameterGenerator('attachment', 'bool', false),
            new ParameterGenerator('cached', 'bool', true),
            new ParameterGenerator('options', 'array', []),
        ]);


        $code = <<<CODE
\$tmpFile = \$this->getSdk()->getCacheDir() . '/'.md5(__FILE__).'/'.md5(__METHOD__).'/'.md5(\$parentId.\$id.'-'.\$fingerPrint);
if (!\$cached || !file_exists(\$tmpFile)) {
    // the fingerPrint is ignored.
    \$data  = \$this->getSdk()->get(sprintf('{$definition['route']}', \$parentId, \$id), ['{$definition['property']}', '{$definition['contentTypeField']}'], \$options);
    if (!is_dir(dirname(\$tmpFile))) {
        mkdir(dirname(\$tmpFile), 0777, true);
    }
    file_put_contents(\$tmpFile, base64_decode(\$data['{$definition['property']}']));
    file_put_contents(\$tmpFile.'.format', \$data['{$definition['contentTypeField']}']);
}
\$response = new \Symfony\Component\HttpFoundation\BinaryFileResponse(new \SplFileInfo(\$tmpFile));
\$response->headers->set('Content-Type', file_get_contents(\$tmpFile.'.format'));
\$response->setContentDisposition(
    \$attachment ? \Symfony\Component\HttpFoundation\ResponseHeaderBag::DISPOSITION_ATTACHMENT : \Symfony\Component\HttpFoundation\ResponseHeaderBag::DISPOSITION_INLINE,
    \$fileName ? \$fileName : null
);
return \$response;
CODE;

        $zMethod->setBody($code);
    }
    /**
     * @param MethodGenerator $zMethod
     * @param array           $definition
     *
     * @Annotation\CodeGeneratorMethodType("crud.sub.streamProperty")
     */
    public function generateCrudSubStreamPropertyMethod(MethodGenerator $zMethod, $definition = [])
    {
        $cache = false;
        $fileName = false;
        $attachment = false;

        $definition += ['format' => null, 'property' => 'content'];
        $definition['route'] = str_replace(['{parentId}', '{id}'], '%s', $definition['route']);

        $paramTags = [
            new ParamTag('parentId', ['string'], sprintf('ID of the %s', $definition['type'])),
            new ParamTag('id', ['string'], sprintf('ID of the %s', $definition['subType'])),
        ];

        $params = [
            new ParameterGenerator('parentId', 'string'),
            new ParameterGenerator('id', 'string'),
        ];

        if (!isset($definition['fileNameFeatureEnabled']) || true === $definition['fileNameFeatureEnabled']) {
            $paramTags[] = new ParamTag('fileName', ['string'], 'The file name to send to the browser');
            $params[] = new ParameterGenerator('fileName', 'string');
            $fileName = true;
        }
        if (!isset($definition['attachmentFeatureEnabled']) || true === $definition['attachmentFeatureEnabled']) {
            $paramTags[] = new ParamTag('attachment', ['bool'], 'Send as attachment or inline');
            $params[] = new ParameterGenerator('attachment', 'bool', false);
            $attachment = true;
        }
        if (!isset($definition['cacheFeatureEnabled']) || true === $definition['cacheFeatureEnabled']) {
            $paramTags[] = new ParamTag('cached', ['bool'], 'Cache the content locally');
            $params[] = new ParameterGenerator('cached', 'bool', true);
            $cache = true;
        }

        $paramTags[] = new ParamTag('options', ['array'], 'Options');
        $paramTags[] = new ReturnTag(['\Symfony\Component\HttpFoundation\BinaryFileResponse']);
        $paramTags[] = new ThrowsTag(['\\Exception'], 'if an error occured');

        $params[] = new ParameterGenerator('options', 'array', []);

        $zMethod->setDocBlock(new DocBlockGenerator(
            sprintf('Return a Symfony Binary File Response of the %s of a %s %s specified by %s id and %s id', $definition['property'], $definition['type'], $definition['subType'], $definition['type'], $definition['subType']),
            null,
            $paramTags
        ));
        $zMethod->setParameters($params);

        $code = '';

        if (false === $cache) {
            $code .= "\n".'$cached = false;';
        }

        if (false === $attachment) {
            $code .= "\n".'$attachment = false;';
        }

        if (false === $fileName) {
            $code .= "\n".'$fileName = false;';
        }

        $code .= <<<CODE

\$tmpFile = \$this->getSdk()->getCacheDir() . '/'.md5(__FILE__).'/'.md5(__METHOD__).'/'.md5(\$parentId.\$id);
if (!\$cached || !file_exists(\$tmpFile)) {
    // the fingerPrint is ignored.
    \$data  = \$this->getSdk()->get(sprintf('{$definition['route']}', \$parentId, \$id), ['id', '{$definition['property']}', '{$definition['contentTypeField']}'], \$options);
    if (!is_dir(dirname(\$tmpFile))) {
        mkdir(dirname(\$tmpFile), 0777, true);
    }
    file_put_contents(\$tmpFile, base64_decode(\$data['{$definition['property']}']));
    file_put_contents(\$tmpFile.'.format', \$data['{$definition['contentTypeField']}']);
}
\$response = new \Symfony\Component\HttpFoundation\BinaryFileResponse(new \SplFileInfo(\$tmpFile));
\$response->headers->set('Content-Type', file_get_contents(\$tmpFile.'.format'));
\$response->setContentDisposition(
    \$attachment ? \Symfony\Component\HttpFoundation\ResponseHeaderBag::DISPOSITION_ATTACHMENT : \Symfony\Component\HttpFoundation\ResponseHeaderBag::DISPOSITION_INLINE,
    \$fileName ? \$fileName : null
);
return \$response;
CODE;

        $zMethod->setBody($code);
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
                        throw new \RuntimeException(sprintf("Unsupported key formatter: %s", $definition['transform']['key'.ucfirst($key)]), 500);
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
