<?php

/*
 * This file is part of the COMMON package.
 *
 * (c) itiQiti SAS <cto@itiqiti.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Itq\Common\Traits;

use Symfony\Component\ExpressionLanguage\ExpressionLanguage;

/**
 * ExpressionLanguageAware trait.
 *
 * @author Olivier Hoareau <olivier@itiqiti.com>
 */
trait ExpressionLanguageAwareTrait
{
    /**
     * @param string $key
     * @param mixed  $service
     *
     * @return $this
     */
    protected abstract function setService($key, $service);
    /**
     * @param string $key
     *
     * @return mixed
     */
    protected abstract function getService($key);
    /**
     * @param ExpressionLanguage $service
     *
     * @return $this
     */
    public function setExpressionLanguage(ExpressionLanguage $service)
    {
        return $this->setService('expressionLanguage', $service);
    }
    /**
     * @return ExpressionLanguage
     */
    public function getExpressionLanguage()
    {
        return $this->getService('expressionLanguage');
    }
}
