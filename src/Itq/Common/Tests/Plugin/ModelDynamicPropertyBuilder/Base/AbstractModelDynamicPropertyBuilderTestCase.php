<?php

/*
 * This file is part of the COMMON package.
 *
 * (c) itiQiti SAS <opensource@itiqiti.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Itq\Common\Tests\Plugin\ModelDynamicPropertyBuilder\Base;

use Itq\Common\Tests\Plugin\Base\AbstractPluginTestCase;
use Itq\Common\Plugin\ModelDynamicPropertyBuilderInterface;

/**
 * @author itiQiti Dev Team <opensource@itiqiti.com>
 */
abstract class AbstractModelDynamicPropertyBuilderTestCase extends AbstractPluginTestCase
{
    /**
     * @return ModelDynamicPropertyBuilderInterface
     */
    public function b()
    {
        /** @noinspection PhpIncompatibleReturnTypeInspection */

        return $this->p();
    }
}
