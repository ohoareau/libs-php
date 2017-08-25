<?php

/*
 * This file is part of the COMMON package.
 *
 * (c) itiQiti SAS <opensource@itiqiti.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tests\Itq\Common\Plugin\ModelCleaner;

use Itq\Common\Plugin\ModelCleaner\TriggerTrackersModelCleaner;
use Itq\Common\Tests\Plugin\ModelCleaner\Base\AbstractModelCleanerTestCase;

/**
 * @author itiQiti Dev Team <opensource@itiqiti.com>
 *
 * @group plugins
 * @group plugins/models
 * @group plugins/models/cleaners
 * @group plugins/models/cleaners/trigger-trackers
 */
class TriggerTrackersModelCleanerTest extends AbstractModelCleanerTestCase
{
    /**
     * @return TriggerTrackersModelCleaner
     */
    public function c()
    {
        /** @noinspection PhpIncompatibleReturnTypeInspection */

        return parent::c();
    }
    /**
     * @return array
     */
    public function constructor()
    {
        return [$this->mockedMetaDataService(), $this->mockedTrackerService()];
    }
}
