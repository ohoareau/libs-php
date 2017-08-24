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

use Exception;

/**
 * Default Recipients Parameter Aware trait.
 *
 * @author itiQiti Dev Team <opensource@itiqiti.com>
 */
trait DefaultRecipientsParameterAwareTrait
{
    /**
     * @param array $defaultRecipients
     *
     * @return $this
     */
    public function setDefaultRecipients(array $defaultRecipients)
    {
        return $this->setParameter('defaultRecipients', $defaultRecipients);
    }
    /**
     * @return array
     *
     * @throws Exception
     */
    public function getDefaultRecipients()
    {
        return $this->getParameter('defaultRecipients');
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
