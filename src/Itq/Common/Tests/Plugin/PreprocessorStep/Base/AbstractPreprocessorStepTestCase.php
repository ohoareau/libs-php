<?php

/*
 * This file is part of the COMMON package.
 *
 * (c) itiQiti SAS <opensource@itiqiti.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Itq\Common\Tests\Plugin\PreprocessorStep\Base;

use Itq\Common\Plugin\PreprocessorStepInterface;
use Itq\Common\Tests\Plugin\Base\AbstractPluginTestCase;

/**
 * @author itiQiti Dev Team <opensource@itiqiti.com>
 */
abstract class AbstractPreprocessorStepTestCase extends AbstractPluginTestCase
{
    /**
     * @return PreprocessorStepInterface
     */
    public function s()
    {
        /** @noinspection PhpIncompatibleReturnTypeInspection */

        return $this->p();
    }
}
