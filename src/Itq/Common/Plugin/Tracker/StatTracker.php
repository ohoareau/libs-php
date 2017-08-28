<?php

/*
 * This file is part of the COMMON package.
 *
 * (c) itiQiti SAS <opensource@itiqiti.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Itq\Common\Plugin\Tracker;

use Itq\Common\Traits;
use Itq\Common\Service;

/**
 * @author itiQiti Dev Team <opensource@itiqiti.com>
 */
class StatTracker extends Base\AbstractTracker
{
    use Traits\ServiceAware\ModelStatsServiceAwareTrait;
    /**
     * @param Service\ModelStatsService $modelStatsService
     */
    public function __construct(Service\ModelStatsService $modelStatsService)
    {
        $this->setModelStatsService($modelStatsService);
    }
    /**
     * @param array $definition
     * @param mixed $data
     * @param array $options
     *
     * @return void
     */
    public function track(array $definition, $data, array $options = [])
    {
        $this->getModelStatsService()->track($definition, $data, $options);
    }
}
