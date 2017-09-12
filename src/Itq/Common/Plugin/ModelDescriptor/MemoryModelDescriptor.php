<?php

/*
 * This file is part of the COMMON package.
 *
 * (c) itiQiti SAS <opensource@itiqiti.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Itq\Common\Plugin\ModelDescriptor;

use Closure;

/**
 * @author itiQiti Dev Team <opensource@itiqiti.com>
 */
class MemoryModelDescriptor extends Base\AbstractModelDescriptor
{
    /**
     * @param null|array|Closure $description
     */
    public function __construct($description = null)
    {
        $this->setDescription($description);
    }
    /**
     * @param null|array|Closure $description
     *
     * @return $this
     */
    public function setDescription($description)
    {
        return $this->setPArameter('description', $description);
    }
    /**
     * @return null|array|Closure
     */
    public function getDescription()
    {
        return $this->getParameterIfExists('description');
    }
    /**
     * @param string $id
     * @param array  $options
     *
     * @return array|null
     *
     * @throws \Exception
     */
    public function describe($id, array $options = [])
    {
        $description = $this->getDescription();

        if ($this->isPhpCallable($description)) {
            $description = $this->callPhpCallable($description, [$id, $options]);
        }

        return is_array($description) ? $description : [];
    }
}
