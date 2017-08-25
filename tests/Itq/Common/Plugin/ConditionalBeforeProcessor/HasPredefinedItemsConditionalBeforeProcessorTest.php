<?php

/*
 * This file is part of the COMMON package.
 *
 * (c) itiQiti SAS <opensource@itiqiti.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tests\Itq\Common\Plugin\ConditionalBeforeProcessor;

use Itq\Common\Plugin\ConditionalBeforeProcessor\HasPredefinedItemsConditionalBeforeProcessor;
use Itq\Common\Tests\Plugin\ConditionalBeforeProcessor\Base\AbstractConditionalBeforeProcessorTestCase;

/**
 * @author itiQiti Dev Team <opensource@itiqiti.com>
 *
 * @group plugins
 * @group plugins/processors
 * @group plugins/processors/conditional-before
 * @group plugins/processors/conditional-before/has-predefined-items-conditional-before-processor
 */
class HasPredefinedItemsConditionalBeforeProcessorTest extends AbstractConditionalBeforeProcessorTestCase
{
    /**
     * @return HasPredefinedItemsConditionalBeforeProcessor
     */
    public function p()
    {
        /** @noinspection PhpIncompatibleReturnTypeInspection */

        return parent::p();
    }
}
