<?php

/*
 * This file is part of the COMMON package.
 *
 * (c) itiQiti SAS <opensource@itiqiti.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Itq\Common\Traits\Controller;

use Exception;
use Symfony\Component\HttpFoundation\RequestStack;

/**
 * @author itiQiti Dev Team <opensource@itiqiti.com>
 */
trait LocaleControllerTrait
{
    /**
     * @return RequestStack
     */
    abstract public function getRequestStack();
    /**
     * @return RequestStack
     */
    abstract public function hasRequestStack();
    /**
     * @param string $name
     *
     * @return mixed
     */
    abstract protected function getParameter($name);
    /**
     * @param string $name
     *
     * @return bool
     */
    abstract protected function hasParameter($name);
    /**
     * @param string $locale
     *
     * @return $this
     */
    protected function forceValidLocale($locale = null)
    {
        try {
            if (null === $locale) {
                if (!$this->hasParameter('locale')) {
                    return $this;
                }
                $locale = $this->getParameter('locale');
            }

            if ($this->hasRequestStack()) {
                $requestStack = $this->getRequestStack();

                if (null !== $requestStack->getMasterRequest()) {
                    $requestStack->getMasterRequest()->setLocale($locale);
                }
            }
        } catch (Exception $e) {
            // locale unchanged
        }

        return $this;
    }
}
