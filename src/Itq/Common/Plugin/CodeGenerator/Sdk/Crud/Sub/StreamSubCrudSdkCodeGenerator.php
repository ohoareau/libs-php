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
class StreamSubCrudSdkCodeGenerator extends Base\AbstractSubCrudSdkCodeGenerator
{
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
}
