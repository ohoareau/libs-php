<?php

/*
 * This file is part of the COMMON package.
 *
 * (c) itiQiti SAS <opensource@itiqiti.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tests\Itq\Common\Model;

use Itq\Common\Model\User;
use Itq\Common\Tests\Model\Base\AbstractModelTestCase;

/**
 * @author itiQiti Dev Team <opensource@itiqiti.com>
 *
 * @group models
 * @group models/user
 */
class UserTest extends AbstractModelTestCase
{
    /**
     * @return User
     */
    public function m()
    {
        /** @noinspection PhpIncompatibleReturnTypeInspection */

        return parent::m();
    }
    /**
     * @return array
     */
    public function constructor()
    {
        return [[]];
    }
}
