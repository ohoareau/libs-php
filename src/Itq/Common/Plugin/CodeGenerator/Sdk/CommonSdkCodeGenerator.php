<?php

/*
 * This file is part of the COMMON package.
 *
 * (c) itiQiti SAS <opensource@itiqiti.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Itq\Common\Plugin\CodeGenerator\Sdk;

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
class CommonSdkCodeGenerator extends Base\AbstractSdkCodeGenerator
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
     * @Annotation\CodeGeneratorMethodType("getCurrent")
     */
    public function generateGetCurrentMethod(MethodGenerator $zMethod, $definition = [])
    {
        $model = isset($definition['model']) ? (is_bool($definition['model']) ? $definition['returnType']['type'] : $definition['model']) : null;

        $zMethod->setDocBlock(new DocBlockGenerator(
            sprintf('Return the current %s', $definition['type']),
            null,
            [
                new ParamTag('fields', ['array'], 'List of fields to retrieve'),
                new ParamTag('options', ['array'], 'Options'),
                new ReturnTag([$model ?: 'array']),
                new ThrowsTag(['\\Exception'], 'if an error occured'),
            ]
        ));
        $zMethod->setParameters([
            new ParameterGenerator('fields', 'array', []),
            new ParameterGenerator('options', 'array', []),
        ]);

        $returnBody = sprintf('$this->getSdk()->get(\'%s\', $fields, $options + $this->options)', $definition['route']);

        $zMethod->setBody(
            'return '.($model ? ('$this->modelize('.$returnBody.', $options + $this->options + [\'model\' => \''.$model.'\'])') : $returnBody).';'
        );
    }
}
