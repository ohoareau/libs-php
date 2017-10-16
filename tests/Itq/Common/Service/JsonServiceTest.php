<?php

/*
 * This file is part of the COMMON package.
 *
 * (c) itiQiti SAS <opensource@itiqiti.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tests\Itq\Common\Service;

use function GuzzleHttp\Promise\is_fulfilled;
use Itq\Common\Service\JsonService;
use Itq\Common\Tests\Service\Base\AbstractServiceTestCase;

/**
 * @author itiQiti Dev Team <opensource@itiqiti.com>
 *
 * @group services
 * @group services/json
 */
class JsonServiceTest extends AbstractServiceTestCase
{
    /**
     * @return JsonService
     */
    public function s()
    {
        /** @noinspection PhpIncompatibleReturnTypeInspection */

        return parent::s();
    }

    /**
     * @return array
     */
    protected function getPhpData()
    {
        return [
            'some date', 'associative' => ['key' => [1,2,3, 'string'], 'key2' => 'value']
        ];
    }

    /**
     * @return string
     */
    protected function getJsonData()
    {
        return json_encode($this->getPhpData());
    }

    /**
     * @group unit
     */
    public function testSerialize()
    {
        $options = [JSON_HEX_TAG, JSON_HEX_APOS];
        $actual = $this->s()->serialize($this->getPhpData(), $options);
        $this->assertEquals($this->getJsonData(), $actual);
    }

    /**
     * @group unit
     */
    public function testUnserializeWithNoStringThrowMalformedException()
    {
        $this->expectException('RuntimeException');
        $this->expectExceptionMessage('Only string are JSON unserializable');
        $this->expectExceptionCode(412);
        $this->s()->unserialize(['a tab is not a string']);
    }

    /**
     * @group unit
     */
    public function testUnserialize()
    {
        $this->assertEquals($this->getPhpData(), $this->s()->unserialize($this->getJsonData()));
    }
}
