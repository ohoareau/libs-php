<?php

/*
 * This file is part of the COMMON package.
 *
 * (c) itiQiti SAS <opensource@itiqiti.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Itq\Common\Tests\Plugin\BusinessRule\Base;

use Itq\Common\Plugin\Base\AbstractPlugin;
use Itq\Common\Tests\Plugin\Base\AbstractPluginTestCase;

/**
 * @author itiQiti Dev Team <opensource@itiqiti.com>
 */
abstract class AbstractBusinessRuleTestCase extends AbstractPluginTestCase
{
    /**
     * @return AbstractPlugin
     */
    public function b()
    {
        /** @noinspection PhpIncompatibleReturnTypeInspection */

        return parent::p();
    }
    /**
     * @return AbstractPlugin
     */
    protected function getBusinessRule()
    {
        return $this->getPlugin();
    }
    /**
     * @return string
     */
    protected function getBusinessRuleClass()
    {
        return $this->getPluginClass();
    }
}
