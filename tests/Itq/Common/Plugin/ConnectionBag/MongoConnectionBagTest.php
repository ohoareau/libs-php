<?php

/*
 * This file is part of the COMMON package.
 *
 * (c) itiQiti SAS <opensource@itiqiti.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tests\Itq\Common\Plugin\ConnectionBag;

use Itq\Common\ConnectionInterface;
use Itq\Common\Tests\Base\AbstractTestCase;
use Itq\Common\Plugin\ConnectionBag\MongoConnectionBag;

use RuntimeException;

/**
 * @author itiQiti Dev Team <opensource@itiqiti.com>
 *
 * @group plugins/connection-bags
 * @group plugins/connection-bags/mongo
 */
class MongoConnectionBagTest extends AbstractTestCase
{
    /**
     * @return MongoConnectionBag
     */
    public function o()
    {
        /** @noinspection PhpIncompatibleReturnTypeInspection */

        return parent::o();
    }
    /**
     * @group unit
     */
    public function testConstruct()
    {
        $this->assertNotNull($this->o());
    }
    /**
     * @group unit
     */
    public function testGetConnectionForNoConnectionsThrowException()
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('No connection available');
        $this->expectExceptionCode(500);

        $this->o()->getConnection([]);
    }
    /**
     * @group unit
     */
    public function testGetConnectionForExistingConnectionNameReturnConnection()
    {
        if ($this->warnIfMongoClientNotAvailable()) {
            return;
        }

        $s = new MongoConnectionBag(['a' => []]);

        $c = $s->getConnection(['connection' => 'a']);

        $this->assertTrue($c instanceof ConnectionInterface);
    }
    /**
     * @group unit
     */
    public function testGetConnectionForExistingDefaultConnectionReturnDefaultConnection()
    {
        if ($this->warnIfMongoClientNotAvailable()) {
            return;
        }

        $s = new MongoConnectionBag(['default' => []]);

        $c = $s->getConnection();

        $this->assertTrue($c instanceof ConnectionInterface);
    }
    /**
     * @group unit
     */
    public function testGetConnectionForNotExistingConnectionNameThrowException()
    {
        if ($this->warnIfMongoClientNotAvailable()) {
            return;
        }

        $s = new MongoConnectionBag(['b' => []]);

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('No connection available');
        $this->expectExceptionCode(500);

        $s->getConnection(['connection' => 'a']);
    }
    /**
     * @return bool
     */
    protected function warnIfMongoClientNotAvailable()
    {
        if (!class_exists('MongoClient')) {
            $this->markTestSkipped('MongoClient not available');

            return true;
        }

        return false;
    }
}
