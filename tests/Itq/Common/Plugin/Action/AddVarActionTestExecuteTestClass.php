<?php

/*
 * This file is part of the COMMON package.
 *
 * (c) itiQiti SAS <opensource@itiqiti.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tests\Itq\Common\Plugin\Action;

/**
 * @author itiQiti Dev Team <opensource@itiqiti.com>
 */
class AddVarActionTestExecuteTestClass
{
    /**
     * @var bool
     */
    public $called = false;
    /**
     *
     */
    public function m1()
    {
        $this->called = true;
    }
}
