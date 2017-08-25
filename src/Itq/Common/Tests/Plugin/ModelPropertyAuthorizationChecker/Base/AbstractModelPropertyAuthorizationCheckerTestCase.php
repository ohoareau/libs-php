<?php

/*
 * This file is part of the COMMON package.
 *
 * (c) itiQiti SAS <opensource@itiqiti.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Itq\Common\Tests\Plugin\ModelPropertyAuthorizationChecker\Base;

use Itq\Common\Tests\Plugin\Base\AbstractPluginTestCase;
use Itq\Common\Plugin\ModelPropertyAuthorizationCheckerInterface;

/**
 * @author itiQiti Dev Team <opensource@itiqiti.com>
 */
abstract class AbstractModelPropertyAuthorizationCheckerTestCase extends AbstractPluginTestCase
{
    /**
     * @return ModelPropertyAuthorizationCheckerInterface
     */
    public function c()
    {
        /** @noinspection PhpIncompatibleReturnTypeInspection */

        return $this->p();
    }
}
