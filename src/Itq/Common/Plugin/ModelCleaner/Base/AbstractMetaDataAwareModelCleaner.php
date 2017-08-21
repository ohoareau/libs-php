<?php

/*
 * This file is part of the COMMON package.
 *
 * (c) itiQiti SAS <opensource@itiqiti.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Itq\Common\Plugin\ModelCleaner\Base;

use Itq\Common\Traits;
use Itq\Common\Service;

/**
 * @author itiQiti Dev Team <opensource@itiqiti.com>
 */
abstract class AbstractMetaDataAwareModelCleaner extends AbstractModelCleaner
{
    use Traits\ServiceAware\MetaDataServiceAwareTrait;
    /**
     * @param Service\MetaDataService $metaDataService
     */
    public function __construct(Service\MetaDataService $metaDataService)
    {
        $this->setMetaDataService($metaDataService);
    }
}
