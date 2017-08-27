<?php

/*
 * This file is part of the COMMON package.
 *
 * (c) itiQiti SAS <opensource@itiqiti.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tests\Itq\Common\Service;

use Itq\Common\Service;
use Itq\Common\Tests\Service\Base\AbstractServiceTestCase;

/**
 * @author itiQiti Dev Team <opensource@itiqiti.com>
 *
 * @group services
 * @group services/model
 */
class ModelServiceTest extends AbstractServiceTestCase
{
    /**
     * @return Service\ModelService
     */
    public function s()
    {
        /** @noinspection PhpIncompatibleReturnTypeInspection */

        return parent::s();
    }
    /**
     * @return array
     */
    public function constructor()
    {
        return [
            $this->mocked('cleaner', Service\Model\ModelCleanerServiceInterface::class),
            $this->mocked('restricer', Service\Model\ModelRestricterServiceInterface::class),
            $this->mocked('updateEnricher', Service\Model\ModelUpdateEnricherServiceInterface::class),
            $this->mocked('objectPopulator', Service\Model\ModelObjectPopulatorServiceInterface::class),
            $this->mocked('refresher', Service\Model\ModelRefresherServiceInterface::class),
            $this->mocked('fieldListFilter', Service\Model\ModelFieldListFilterServiceInterface::class),
            $this->mocked('dynamicUrlBuilder', Service\Model\ModelDynamicUrlBuilderServiceInterface::class),
            $this->mocked('propertyLinearizer', Service\Model\ModelPropertyLinearizerServiceInterface::class),
        ];
    }
}
