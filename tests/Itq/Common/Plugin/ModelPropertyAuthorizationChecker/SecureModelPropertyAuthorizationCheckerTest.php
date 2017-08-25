<?php

/*
 * This file is part of the COMMON package.
 *
 * (c) itiQiti SAS <opensource@itiqiti.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tests\Itq\Common\Plugin\ModelPropertyAuthorizationChecker;

use Itq\Common\Plugin\ModelPropertyAuthorizationChecker\SecureModelPropertyAuthorizationChecker;
use Itq\Common\Tests\Plugin\ModelPropertyAuthorizationChecker\Base\AbstractModelPropertyAuthorizationCheckerTestCase;

/**
 * @author itiQiti Dev Team <opensource@itiqiti.com>
 *
 * @group plugins
 * @group plugins/models
 * @group plugins/models/property-authorization-checkers
 * @group plugins/models/property-authorization-checkers/secure
 */
class SecureModelPropertyAuthorizationCheckerTest extends AbstractModelPropertyAuthorizationCheckerTestCase
{
    /**
     * @return SecureModelPropertyAuthorizationChecker
     */
    public function c()
    {
        /** @noinspection PhpIncompatibleReturnTypeInspection */

        return parent::c();
    }
    /**
     * @return array
     */
    public function constructor()
    {
        return [$this->mockedMetaDataService(), $this->mockedAuthorizationChecker()];
    }
}
