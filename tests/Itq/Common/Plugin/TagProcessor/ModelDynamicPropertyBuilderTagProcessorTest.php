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

use Itq\Common\Plugin\TagProcessor\ModelDynamicPropertyBuilderTagProcessor;
use Itq\Common\Tests\Plugin\TagProcessor\Base\AbstractTagProcessorTestCase;

/**
 * @author itiQiti Dev Team <opensource@itiqiti.com>
 *
 * @group plugins
 * @group plugins/processors
 * @group plugins/processors/tags
 * @group plugins/processors/tags/model-dynamic-property-builder
 */
class ModelDynamicPropertyBuilderTagProcessorTest extends AbstractTagProcessorTestCase
{
    /**
     * @return ModelDynamicPropertyBuilderTagProcessor
     */
    public function p()
    {
        /** @noinspection PhpIncompatibleReturnTypeInspection */

        return parent::p();
    }
}
