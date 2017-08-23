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
class FindCrudSdkCodeGenerator extends Base\AbstractCrudSdkCodeGenerator
{
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
}
