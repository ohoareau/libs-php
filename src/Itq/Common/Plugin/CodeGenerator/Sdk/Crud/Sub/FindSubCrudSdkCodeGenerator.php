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
class FindSubCrudSdkCodeGenerator extends Base\AbstractSubCrudSdkCodeGenerator
{
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
}
