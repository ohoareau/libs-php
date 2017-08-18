<?php

/*
 * This file is part of the COMMON package.
 *
 * (c) itiQiti SAS <opensource@itiqiti.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Itq\Common\Traits;

use Symfony\Component\Translation\TranslatorInterface;

/**
 * TranslatorAware trait.
 *
 * @author itiQiti Dev Team <opensource@itiqiti.com>
 */
trait TranslatorAwareTrait
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
     * @param TranslatorInterface $translator
     *
     * @return $this
     */
    public function setTranslator(TranslatorInterface $translator)
    {
        return $this->setService('translator', $translator);
    }
    /**
     * @return TranslatorInterface
     */
    public function getTranslator()
    {
        return $this->getService('translator');
    }
    /**
     * @return bool
     */
    public function hasTranslator()
    {
        return $this->hasService('translator');
    }
    /**
     * @param string $pattern
     * @param array  $params
     *
     * @return string
     */
    protected function translate($pattern, $params = [])
    {
        return $this->getTranslator()->trans($pattern, $params);
    }
}
