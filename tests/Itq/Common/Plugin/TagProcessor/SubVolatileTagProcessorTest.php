<?php

/*
 * This file is part of the COMMON package.
 *
 * (c) itiQiti SAS <opensource@itiqiti.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tests\Itq\Common\Plugin\TagProcessor;

use Itq\Common\Plugin\TagProcessor\SubVolatileTagProcessor;
use Itq\Common\Tests\Plugin\TagProcessor\Base\AbstractTagProcessorTestCase;

/**
 * @author itiQiti Dev Team <opensource@itiqiti.com>
 *
 * @group plugins
 * @group plugins/processors
 * @group plugins/processors/tags
 * @group plugins/processors/tags/sub-volatile
 */
class SubVolatileTagProcessorTest extends AbstractTagProcessorTestCase
{
    /**
     * @return SubVolatileTagProcessor
     */
    public function p()
    {
        /** @noinspection PhpIncompatibleReturnTypeInspection */

        return parent::p();
    }
}
