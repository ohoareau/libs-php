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

use Itq\Common\Tests\Plugin\Base\AbstractPluginTestCase;
use Itq\Common\Plugin\RequestCodec\SudoApiHeaderRequestCodec;

/**
 * @author itiQiti Dev Team <opensource@itiqiti.com>
 *
 * @group plugins
 * @group plugins/request-codecs
 * @group plugins/request-codecs/sudo-api-header
 */
class SudoApiHeaderRequestCodecTest extends AbstractPluginTestCase
{
    /**
     * @return SudoApiHeaderRequestCodec
     */
    public function c()
    {
        /** @noinspection PhpIncompatibleReturnTypeInspection */

        return parent::p();
    }
}
