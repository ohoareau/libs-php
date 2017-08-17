<?php

/*
 * This file is part of the COMMON package.
 *
 * (c) itiQiti SAS <opensource@itiqiti.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Itq\Common\Plugin\ExceptionDescriptor;

use Exception;
use Itq\Common\Exception\NamedBusinessRuleException;

/**
 * @author itiQiti Dev Team <opensource@itiqiti.com>
 */
class NamedBusinessRuleExceptionExceptionDescriptor extends Base\AbstractExceptionDescriptor
{
    /**
     * @param Exception $exception
     *
     * @return bool
     */
    public function supports(Exception $exception)
    {
        return $exception instanceof NamedBusinessRuleException;
    }
    /**
     * @param Exception $exception
     *
     * @return array
     */
    public function describe(Exception $exception)
    {
        /** @var NamedBusinessRuleException $exception */
        $code                        = $exception->getCode();
        $data                        = [];
        $data['code']                = $exception->getCode();
        $data['message']             = $exception->getMessage();
        $data['type']                = 'business';
        $data['subType']             = isset($exception->getData()['subType']) ? $exception->getData()['subType'] : null;
        $data['model']               = isset($exception->getData()['model']) ? $exception->getData()['model'] : null;
        $data['operation']           = isset($exception->getData()['operation']) ? $exception->getData()['operation'] : null;
        $data['data']                = $exception->getData();
        unset($data['data']['subType'], $data['data']['model'], $data['data']['operation']);

        return [$code, $data];
    }
}
