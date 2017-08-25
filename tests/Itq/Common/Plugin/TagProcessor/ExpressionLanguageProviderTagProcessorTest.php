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

use Itq\Common\Plugin\TagProcessor\ExpressionLanguageProviderTagProcessor;
use Itq\Common\Tests\Plugin\TagProcessor\Base\AbstractTagProcessorTestCase;

/**
 * @author itiQiti Dev Team <opensource@itiqiti.com>
 *
 * @group plugins
 * @group plugins/processors
 * @group plugins/processors/tags
 * @group plugins/processors/tags/expression-language-provider
 */
class ExpressionLanguageProviderTagProcessorTest extends AbstractTagProcessorTestCase
{
    /**
     * @return ExpressionLanguageProviderTagProcessor
     */
    public function p()
    {
        /** @noinspection PhpIncompatibleReturnTypeInspection */

        return parent::p();
    }
}
