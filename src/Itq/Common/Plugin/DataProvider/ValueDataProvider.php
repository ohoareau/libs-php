<?php

/*
 * This file is part of the COMMON package.
 *
 * (c) itiQiti SAS <opensource@itiqiti.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Itq\Common\Plugin\DataProvider;

use Closure;

/**
 * @author itiQiti Dev Team <opensource@itiqiti.com>
 */
class ValueDataProvider extends Base\AbstractDataProvider
{
    /**
     * @param mixed $value
     */
    public function __construct($value = null)
    {
        $this->setValue($value);
    }
    /**
     * @param mixed $value
     *
     * @return $this
     */
    public function setValue($value)
    {
        return $this->setParameter('value', $value);
    }
    /**
     * @return mixed
     */
    public function getValue()
    {
        return $this->getParameter('value');
    }
    /**
     * @param array $options
     *
     * @return array
     */
    public function provide(array $options = [])
    {
        $value = $this->getValue();

        if ($value instanceof Closure) {
            $value = $value($options);
        }

        return is_array($value) ? $value : [];
    }
}
