<?php

/*
 * This file is part of the COMMON package.
 *
 * (c) itiQiti SAS <opensource@itiqiti.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tests\Itq\Common\Plugin\ModelDynamicPropertyBuilder;

use Itq\Common\Plugin\ModelDynamicPropertyBuilder\StorageUrlsModelDynamicPropertyBuilder;
use Itq\Common\Tests\Plugin\ModelDynamicPropertyBuilder\Base\AbstractModelDynamicPropertyBuilderTestCase;

/**
 * @author itiQiti Dev Team <opensource@itiqiti.com>
 *
 * @group plugins
 * @group plugins/models
 * @group plugins/models/dynamic-property-builders
 * @group plugins/models/dynamic-property-builders/storage-urls
 */
class StorageUrlsModelDynamicPropertyBuilderTest extends AbstractModelDynamicPropertyBuilderTestCase
{
    /**
     * @return StorageUrlsModelDynamicPropertyBuilder
     */
    public function b()
    {
        /** @noinspection PhpIncompatibleReturnTypeInspection */

        return parent::b();
    }
    /**
     * @return array
     */
    public function constructor()
    {
        return [$this->mockedMetaDataService(), $this->mockedCrudService(), $this->mockedGeneratorService()];
    }
}
