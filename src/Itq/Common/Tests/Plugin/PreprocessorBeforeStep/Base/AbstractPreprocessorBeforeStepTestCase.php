<?php

/*
 * This file is part of the COMMON package.
 *
 * (c) itiQiti SAS <opensource@itiqiti.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Itq\Common\Tests\Plugin\PreprocessorBeforeStep\Base;

use Itq\Common\Plugin\PreprocessorBeforeStepInterface;
use Itq\Common\Tests\Plugin\Base\AbstractPluginTestCase;

/**
 * @author itiQiti Dev Team <opensource@itiqiti.com>
 */
abstract class AbstractPreprocessorBeforeStepTestCase extends AbstractPluginTestCase
{
    /**
     * @return PreprocessorBeforeStepInterface
     */
    public function s()
    {
        /** @noinspection PhpIncompatibleReturnTypeInspection */

        return parent::p();
    }
}
