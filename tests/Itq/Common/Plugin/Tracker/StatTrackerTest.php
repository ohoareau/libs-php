<?php

/*
 * This file is part of the COMMON package.
 *
 * (c) itiQiti SAS <opensource@itiqiti.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tests\Itq\Common\Plugin\Tracker;

use Itq\Common\Plugin\Tracker\StatTracker;
use Itq\Common\Tests\Plugin\Tracker\Base\AbstractTrackerTestCase;

/**
 * @author itiQiti Dev Team <opensource@itiqiti.com>
 *
 * @group plugins
 * @group plugins/trackers
 * @group plugins/trackers/stat-tracker
 */
class StatTrackerTest extends AbstractTrackerTestCase
{
    /**
     * @return StatTracker
     */
    public function t()
    {
        /** @noinspection PhpIncompatibleReturnTypeInspection */

        return parent::t();
    }
    /**
     * @return array
     */
    public function constructor()
    {
        return [$this->mockedModelStatsService()];
    }
}
