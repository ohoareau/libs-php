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

use Itq\Common\Plugin\Action\SmsAction;
use Itq\Common\Tests\Plugin\Action\Base\AbstractActionTestCase;

/**
 * @author itiQiti Dev Team <opensource@itiqiti.com>
 *
 * @group plugins
 * @group plugins/actions
 * @group plugins/actions/sms
 */
class SmsActionTest extends AbstractActionTestCase
{
    /**
     * @return SmsAction
     */
    public function a()
    {
        /** @noinspection PhpIncompatibleReturnTypeInspection */

        return parent::a();
    }
    /**
     * @return array
     */
    public function constructor()
    {
        return [
            $this->mockedTemplateService(),
            $this->mockedTranslator(),
            $this->mockedAttachmentService(),
            $this->mockedCustomizerService(),
            $this->mockedEventDispatcher(),
            [],
            [],
            'test',
            $this->mockedRequestStack(),
            $this->mockedTenantService(),
            'fr_FR',
        ];
    }
}
