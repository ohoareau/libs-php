<?php

/*
 * This file is part of the COMMON package.
 *
 * (c) itiQiti SAS <opensource@itiqiti.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tests\Itq\Common\Adapter\System;

use Itq\Common\Tests\Base\AbstractTestCase;
use Itq\Common\Adapter\System\NativeSystemAdapter;

/**
 * @author itiQiti Dev Team <opensource@itiqiti.com>
 *
 * @group adapters
 * @group adapters/system
 * @group adapters/system/native
 */
class NativeSystemAdapterTest extends AbstractTestCase
{
    /**
     * @return NativeSystemAdapter
     */
    public function a()
    {
        /** @noinspection PhpIncompatibleReturnTypeInspection */

        return parent::o();
    }
    /**
     * @group integ
     */
    public function testGetTempDirectory()
    {
        $this->assertEquals(sys_get_temp_dir(), $this->a()->getTempDirectory());
    }
    /**
     * @group integ
     */
    public function testExec()
    {
        $output = [];
        $return = 0;

        ob_start();
        $actualReturn = $this->a()->exec('echo hello', $output, $return);
        $output = ob_get_clean();

        $this->assertEquals('hello', $actualReturn);
        $this->assertEquals(0, $return);
        $this->assertEquals('', rtrim($output));
    }
    /**
     * @group integ
     */
    public function testPassthru()
    {
        $return = 0;

        ob_start();
        $this->a()->passthru('echo hello', $return);
        $output = ob_get_clean();

        $this->assertEquals(0, $return);
        $this->assertEquals('hello', rtrim($output));
    }
    /**
     * @group integ
     */
    public function testMicrotime()
    {
        $a = microtime(true);
        $b = $this->a()->microtime();
        $c = microtime(true);

        $this->assertTrue(is_float($b));
        $this->assertLessThanOrEqual($c, $b);
        $this->assertGreaterThanOrEqual($a, $b);
    }
    /**
     * @group integ
     */
    public function testHostname()
    {
        $this->assertEquals(gethostname(), $this->a()->hostname());
    }
    /**
     * @group integ
     */
    public function testGetTimeZone()
    {
        $this->assertEquals(date_default_timezone_get(), $this->a()->getTimeZone());
    }
}
