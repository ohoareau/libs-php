<?php

/*
 * This file is part of the COMMON package.
 *
 * (c) itiQiti SAS <opensource@itiqiti.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tests\Itq\Common\Plugin\RequestCodec;

use Itq\Common\Service\DateService;
use Symfony\Component\HttpFoundation\Request;
use Itq\Common\Plugin\RequestCodec\InstanceApiHeaderRequestCodec;
use Itq\Common\Tests\Plugin\RequestCodec\Base\AbstractRequestCodecTestCase;

/**
 * @author itiQiti Dev Team <opensource@itiqiti.com>
 *
 * @group plugins
 * @group plugins/request-codecs
 * @group plugins/request-codecs/instance-api-header
 */
class InstanceApiHeaderRequestCodecTest extends AbstractRequestCodecTestCase
{
    /**
     * @return InstanceApiHeaderRequestCodec
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
        return [$this->mockedDateService(), 'thesecret'];
    }
    /**
     * @group unit
     */
    public function testDecode()
    {
        $this->assertNull($this->c()->decode(new Request()));
    }
}
