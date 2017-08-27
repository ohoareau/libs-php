<?php

/*
 * This file is part of the COMMON package.
 *
 * (c) itiQiti SAS <opensource@itiqiti.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tests\Itq\Common\Service\Model;

use Itq\Common\Service;
use Itq\Common\Tests\Service\Base\AbstractServiceTestCase;

/**
 * @author itiQiti Dev Team <opensource@itiqiti.com>
 *
 * @group services/model
 * @group services/model/object-populator
 */
class ModelObjectPopulatorServiceTest extends AbstractServiceTestCase
{
    /**
     * @return Service\Model\ModelObjectPopulatorService
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
            $this->mockedMetaDataService(),
            $this->mockedStorageService(),
            $this->mocked('propertyMutator', Service\Model\ModelPropertyMutatorServiceInterface::class),
            $this->mocked('dynamicPropertyBuilder', Service\Model\ModelDynamicPropertyBuilderServiceInterface::class),
        ];
    }
}
