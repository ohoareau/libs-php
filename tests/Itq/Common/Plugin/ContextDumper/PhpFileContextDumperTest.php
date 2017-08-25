<?php

/*
 * This file is part of the COMMON package.
 *
 * (c) itiQiti SAS <opensource@itiqiti.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tests\Itq\Common\Plugin\ContextDumper;

use Itq\Common\Plugin\ContextDumper\PhpFileContextDumper;
use Itq\Common\Tests\Plugin\ContextDumper\Base\AbstractContextDumperTestCase;

/**
 * @author itiQiti Dev Team <opensource@itiqiti.com>
 *
 * @group plugins
 * @group plugins/context-dumpers
 * @group plugins/context-dumpers/php-file
 */
class PhpFileContextDumperTest extends AbstractContextDumperTestCase
{
    /**
     * @return PhpFileContextDumper
     */
    public function d()
    {
        /** @noinspection PhpIncompatibleReturnTypeInspection */

        return parent::d();
    }
    /**
     * @return array
     */
    public function constructor()
    {
        return [$this->mockedFilesystemService()];
    }
}
