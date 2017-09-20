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

use Itq\Common\InstanceProviderInterface;
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
    /**
     * @group unit
     */
    public function testDecodeForValidHeader()
    {
        $dt = new \DateTime();
        $d  = $dt->format(\DateTime::ISO8601);
        $this->mockedDateService()->expects($this->once())->method('convertStringToDateTime')->willReturn($dt);
        $this->mockedDateService()->expects($this->once())->method('convertDateTimeToString')->willReturn($d);
        $instanceProvider = $this->mocked('instanceProvider', InstanceProviderInterface::class);
        $instanceProvider->expects($this->once())->method('load')->with('abc')->willReturn([]);
        $this->c()->setInstanceProvider($instanceProvider);
        $r = new Request(
            [],
            [],
            [],
            [],
            [],
            [
                'HTTP_X_API_INSTANCE' => sprintf('id: abc, expire: %s, token: %s', $d, base64_encode(sha1(sprintf('abc%s%s', $d, 'thesecret')))),
            ]
        );
        $this->c()->decode($r);
    }
    /**
     * @group unit
     */
    public function testDecodeForValidHeaderAndCleanOption()
    {
        $dt = new \DateTime();
        $d  = $dt->format(\DateTime::ISO8601);
        $this->mockedDateService()->expects($this->once())->method('convertStringToDateTime')->willReturn($dt);
        $this->mockedDateService()->expects($this->once())->method('convertDateTimeToString')->willReturn($d);
        $instanceProvider = $this->mocked('instanceProvider', InstanceProviderInterface::class);
        $instanceProvider->expects($this->once())->method('clean')->with('abc')->willReturn([]);
        $this->c()->setInstanceProvider($instanceProvider);
        $r = new Request(
            [],
            [],
            [],
            [],
            [],
            [
                'HTTP_X_API_INSTANCE' => sprintf('id: abc, expire: %s, token: %s', $d, base64_encode(sha1(sprintf('abc%s%s', $d, 'thesecret')))),
            ]
        );
        $this->c()->decode($r, ['clean' => true]);
    }
}
