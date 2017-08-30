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

use Itq\Common\Traits;
use Itq\Common\Adapter\PhpAdapterInterface;

/**
 * Php Service.
 *
 * @author itiQiti Dev Team <opensource@itiqiti.com>
 */
class PhpService
{
    use Traits\ServiceTrait;
    use Traits\AdapterAware\PhpAdapterAwareTrait;
    /**
     * @param PhpAdapterInterface $phpAdapter
     */
    public function __construct(PhpAdapterInterface $phpAdapter)
    {
        $this->setPhpAdapter($phpAdapter);
    }
    /**
     * @param string $name
     * @param mixed  $default
     *
     * @return mixed
     */
    public function getConstant($name, $default = null)
    {
        if (!$this->getPhpAdapter()->isDefinedConstant($name)) {
            return $default;
        }

        return $this->getPhpAdapter()->getDefinedConstant($name);
    }
    /**
     * @param string $name
     *
     * @return bool
     */
    public function isConstant($name)
    {
        return $this->getPhpAdapter()->isDefinedConstant($name);
    }
    /**
     * @return array
     */
    public function describe()
    {
        return [
            'os'         => $this->getPhpAdapter()->getOs(),
            'version'    => $this->getPhpAdapter()->getVersion(),
            'version_id' => $this->getPhpAdapter()->getVersionId(),
        ];
    }
}
