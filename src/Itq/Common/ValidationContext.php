<?php

/*
 * This file is part of the COMMON package.
 *
 * (c) itiQiti SAS <opensource@itiqiti.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Itq\Common;

use Itq\Common\Traits;

/**
 * @author itiQiti Dev Team <opensource@itiqiti.com>
 */
class ValidationContext implements ValidationContextInterface
{
    use Traits\ServiceTrait;
    /**
     * @param ErrorManagerInterface $errorManager
     */
    public function __construct(ErrorManagerInterface $errorManager)
    {
        $this->setErrorManager($errorManager);
    }
    /**
     * @param string $field
     * @param string $pattern
     * @param array  ...$args
     *
     * @return $this
     */
    public function addFieldError($field, $pattern, ...$args)
    {
        $exception = $this->getErrorManager()->createException($pattern, $args);

        return $this->pushArrayParameterKeyItem('fieldsErrors', $field, ['exception' => $exception, 'message' => $exception->getMessage()]);
    }
    /**
     * @return array
     */
    public function getFieldsErrors()
    {
        return $this->getArrayParameter('fieldsErrors');
    }
}
