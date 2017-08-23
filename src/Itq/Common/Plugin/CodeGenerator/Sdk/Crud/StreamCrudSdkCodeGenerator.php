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
class StreamCrudSdkCodeGenerator extends Base\AbstractCrudSdkCodeGenerator
{
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
}
