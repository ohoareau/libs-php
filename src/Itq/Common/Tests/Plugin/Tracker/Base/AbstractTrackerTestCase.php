<?php

/*
 * This file is part of the COMMON package.
 *
 * (c) itiQiti SAS <opensource@itiqiti.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Itq\Common\Tests\Plugin\Tracker\Base;

use Itq\Common\Plugin\TrackerInterface;
use Itq\Common\Tests\Plugin\Base\AbstractPluginTestCase;

/**
 * @author itiQiti Dev Team <opensource@itiqiti.com>
 */
abstract class AbstractTrackerTestCase extends AbstractPluginTestCase
{
    /**
     * @return TrackerInterface
     */
    public function t()
    {
        /** @noinspection PhpIncompatibleReturnTypeInspection */

        return parent::p();
    }
}
