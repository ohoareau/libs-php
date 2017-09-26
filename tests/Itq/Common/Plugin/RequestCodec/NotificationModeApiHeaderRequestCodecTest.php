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

use Itq\Common\Plugin\RequestCodec\NotificationModeApiHeaderRequestCodec;
use Itq\Common\Tests\Plugin\RequestCodec\Base\AbstractRequestCodecTestCase;

/**
 * @author itiQiti Dev Team <opensource@itiqiti.com>
 *
 * @group plugins
 * @group plugins/request-codecs
 * @group plugins/request-codecs/notification-mode-api-header
 */
class NotificationModeApiHeaderRequestCodecTest extends AbstractRequestCodecTestCase
{
    /**
     * @return NotificationModeApiHeaderRequestCodec
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
}
