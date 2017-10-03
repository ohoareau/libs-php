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

use RuntimeException;
use Itq\Common\Plugin\Action\MailAction;
use Itq\Common\Tests\Plugin\Action\Base\AbstractActionTestCase;

/**
 * @author itiQiti Dev Team <opensource@itiqiti.com>
 *
 * @group plugins
 * @group plugins/actions
 * @group plugins/actions/mail
 */
class MailActionTest extends AbstractActionTestCase
{
    /**
     * @return MailAction
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
    /**
     * @param mixed $expected
     * @param mixed $recipients
     *
     * @group unit
     *
     * @dataProvider getCleanRecipientsData
     */
    public function testCleanRecipients($expected, $recipients)
    {
        if ($expected instanceof RuntimeException) {
            $this->expectExceptionThrown($expected);
        }

        $result = $this->a()->cleanRecipients($recipients);

        if (!($expected instanceof RuntimeException)) {
            $this->assertEquals($expected, $result);
        }
    }
    /**
     * @return array
     */
    public function getCleanRecipientsData()
    {
        return [
            [new RuntimeException('No recipients specified', 412), []],
            [['a@b.com' => 'a@b.com'], ['a@b.com']],
            [['a@b.com' => 'a@b.com'], ['a@b.com' => 'a@b.com']],
            [['a@b.com' => 'b@c.com'], ['a@b.com' => 'b@c.com']],
        ];
    }
}
