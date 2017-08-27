<?php

/*
 * This file is part of the COMMON package.
 *
 * (c) itiQiti SAS <opensource@itiqiti.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Itq\Common\Service\Model;

use Itq\Common\Traits;
use Itq\Common\Service;

/**
 * @author itiQiti Dev Team <opensource@itiqiti.com>
 */
class ModelRefresherService extends Base\AbstractModelRefresherService
{
    use Traits\ServiceAware\MetaDataServiceAwareTrait;
    use Traits\ServiceAware\Model\ModelRestricterServiceAwareTrait;
    /**
     * @param Service\MetaDataService                       $metaDataService
     * @param Service\Model\ModelRestricterServiceInterface $modelRestricterService
     */
    public function __construct(
        Service\MetaDataService $metaDataService,
        Service\Model\ModelRestricterServiceInterface $modelRestricterService
    ) {
        $this->setMetaDataService($metaDataService);
        $this->setModelRestricterService($modelRestricterService);
    }
    /**
     * @param mixed $doc
     * @param array $options
     *
     * @return mixed
     */
    public function refresh($doc, $options = [])
    {
        $this->getModelRestricterService()->restrict($doc, $options);

        foreach ($this->getModelRefreshers() as $refresher) {
            $doc = $refresher->refresh($doc, $options);
        }

        foreach ($doc as $k => $v) {
            if (!is_object($v) || !$this->getMetaDataService()->isModel($v)) {
                continue;
            }
            $doc->$k = $this->refresh($v, $options);
        }

        return $doc;
    }
}
