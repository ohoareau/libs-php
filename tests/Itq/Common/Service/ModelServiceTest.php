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

use Itq\Common\Plugin;
use Itq\Common\Service\ModelService;
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
     * @return ModelService
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
        return [$this->mockedMetaDataService(), $this->mockedStorageService(), $this->mockedDynamicUrlService()];
    }
    /**
     * @param string $type
     * @param string $pluginClass
     * @param array  $methods
     * @param string $getter
     * @param string $adder
     * @param string $optionalTypeForAdder
     *
     * @group unit
     *
     * @dataProvider getPluginsData
     */
    public function testPlugins($type, $pluginClass, array $methods, $getter, $adder, $optionalTypeForAdder = null, $optionalSingleGetter = null)
    {
        $mock = $this->mock($type, $pluginClass, $methods);

        $this->assertEquals([], $this->s()->$getter());
        if (null !== $optionalTypeForAdder) {
            $this->s()->$adder($optionalTypeForAdder, $mock);
            $this->assertEquals([$optionalTypeForAdder => $mock], $this->s()->$getter());
            if (null !== $optionalSingleGetter) {
                $this->assertEquals($mock, $this->s()->$optionalSingleGetter($optionalTypeForAdder));
            }
        } else {
            $this->s()->$adder($mock);
            $this->assertEquals([$mock], $this->s()->$getter());
        }
    }
    /**
     * @return array
     */
    public function getPluginsData()
    {
        return [
            ['refresher', Plugin\ModelRefresherInterface::class, ['refresh'], 'getRefreshers', 'addRefresher'],
            ['cleaner', Plugin\ModelCleanerInterface::class, ['clean'], 'getCleaners', 'addCleaner'],
            ['fieldListFilter', Plugin\ModelFieldListFilterInterface::class, ['filter'], 'getFieldListFilters', 'addFieldListFilter'],
            ['propertyAuthorizationChecker', Plugin\ModelPropertyAuthorizationCheckerInterface::class, ['isAllowed'], 'getPropertyAuthorizationCheckers', 'addPropertyAuthorizationChecker'],
            ['dynamicPropertyBuilder', Plugin\ModelDynamicPropertyBuilderInterface::class, ['supports', 'build'], 'getDynamicPropertyBuilders', 'addDynamicPropertyBuilder'],
            ['propertyLinearizer', Plugin\ModelPropertyLinearizerInterface::class, ['supports', 'linearize'], 'getPropertyLinearizers', 'addPropertyLinearizer'],
            ['updateEnricher', Plugin\ModelUpdateEnricherInterface::class, ['supports', 'enrich'], 'getUpdateEnrichers', 'addUpdateEnricher', 'thetype', 'getUpdateEnricher'],
            ['propertyMutator', Plugin\ModelPropertyMutatorInterface::class, ['supports', 'mutate'], 'getPropertyMutators', 'addPropertyMutator'],
            ['restricter', Plugin\ModelRestricterInterface::class, ['supports', 'restrict'], 'getRestricters', 'addRestricter'],
        ];
    }
}
