<?php

/*
 * This file is part of the COMMON package.
 *
 * (c) itiQiti SAS <opensource@itiqiti.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Itq\Common\Traits\ParameterAware;

/**
 * Default Locale Parameter Aware trait.
 *
 * @author itiQiti Dev Team <opensource@itiqiti.com>
 */
trait DefaultLocaleParameterAwareTrait
{
    /**
     * @param string $locale
     *
     * @return $this
     */
    public function setDefaultLocale($locale)
    {
        return $this->setParameter('defaultLocale', $locale);
    }
    /**
     * @return string
     */
    public function getDefaultLocale()
    {
        return $this->getParameter('defaultLocale');
    }
    /**
     * @param string $key
     * @param mixed  $value
     *
     * @return $this
     */
    abstract protected function setParameter($key, $value);
    /**
     * @param string $key
     * @param mixed  $default
     *
     * @return mixed
     */
    abstract protected function getParameter($key, $default = null);
}
