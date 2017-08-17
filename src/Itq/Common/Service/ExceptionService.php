<?php

/*
 * This file is part of the COMMON package.
 *
 * (c) itiQiti SAS <opensource@itiqiti.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Itq\Common\Service;

use Exception;
use Itq\Common\Traits;
use Symfony\Component\HttpFoundation\RequestStack;
use Itq\Common\Plugin\ExceptionDescriptorInterface;

/**
 * Exception Service.
 *
 * @author itiQiti Dev Team <opensource@itiqiti.com>
 */
class ExceptionService
{
    use Traits\ServiceTrait;
    use Traits\ArrayizerTrait;
    use Traits\RequestStackAwareTrait;
    use Traits\ServiceAware\FormServiceAwareTrait;
    /**
     * @param RequestStack $requestStack
     */
    public function __construct(RequestStack $requestStack)
    {
        $this->setRequestStack($requestStack);
    }
    /**
     * @param ExceptionDescriptorInterface $descriptor
     *
     * @return $this
     */
    public function addDescriptor(ExceptionDescriptorInterface $descriptor)
    {
        $this->setArrayParameterKey('descriptors', uniqid('exception-descriptor'), $descriptor);

        return $this;
    }
    /**
     * @return ExceptionDescriptorInterface[]
     */
    public function getDescriptors()
    {
        return $this->getArrayParameter('descriptors');
    }
    /**
     * @param Exception $e
     *
     * @return array
     */
    public function describe(Exception $e)
    {
        $code    = (100 < $e->getCode() && 600 > $e->getCode()) ? $e->getCode() : 500;
        $headers = [];

        if (method_exists($e, 'getStatusCode')) {
            $code = $e->getStatusCode();
        }
        if (method_exists($e, 'getHeaders')) {
            $headers += $e->getHeaders();
        }

        $data = [
            'code'    => $e->getCode() > 0 ? $e->getCode() : 500,
            'status'  => 'exception',
            'type'    => lcfirst(basename(str_replace('\\', '/', get_class($e)))),
            'message' => $e->getMessage(),
        ];

        foreach ($this->getDescriptors() as $descriptor) {
            if (!$descriptor->supports($e)) {
                continue;
            }

            list ($code, $description) = $descriptor->describe($e);

            $data = $description + $data;
        }

        if ($this->isDebug()) {
            $data['debug'] = $this->arrayize($e->getTrace());
        }

        return ['code' => $code, 'data' => $data, 'headers' => $headers];
    }
    /**
     * @return bool
     */
    protected function isDebug()
    {
        return
               $this->getRequestStack()->getMasterRequest()->headers->has('x-api-debug')
            || ($this->getRequestStack()->getMasterRequest()->query->has('debug')
            && 1 === intval($this->getRequestStack()->getMasterRequest()->query->get('debug')));
    }
}
