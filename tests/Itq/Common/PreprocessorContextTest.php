<?php

/*
 * This file is part of the COMMON package.
 *
 * (c) itiQiti SAS <opensource@itiqiti.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tests\Itq\Common;

use Itq\Common\PreprocessorContext;
use Itq\Common\Tests\Base\AbstractTestCase;

/**
 * @author itiQiti Dev Team <opensource@itiqiti.com>
 *
 * @group objects
 * @group objects/preprocessor-context
 */
class PreprocessorContextTest extends AbstractTestCase
{
    /**
     * @return PreprocessorContext
     */
    public function o()
    {
        /** @noinspection PhpIncompatibleReturnTypeInspection */

        return parent::o();
    }
    /**
     * @group unit
     */
    public function testModelClasses()
    {
        $this->assertEquals([], $this->o()->getModels());
        $this->o()->addModel('Model1', ['id' => 'm1']);
        $this->assertEquals(
            [
                'Model1' => [
                    'embeddedReferences'            => [],
                    'refreshes'                     => [],
                    'generateds'                    => [],
                    'storages'                      => [],
                    'ids'                           => [],
                    'types'                         => [],
                    'fingerPrints'                  => [],
                    'id'                            => 'm1',
                    'workflows'                     => [],
                    'triggers'                      => [],
                    'cachedLists'                   => [],
                    'embeddeds'                     => [],
                    'embeddedLists'                 => [],
                    'basicLists'                    => [],
                    'hashLists'                     => [],
                    'references'                    => [],
                    'virtualEmbeddedReferenceLists' => [],
                    'updateEnrichments'             => [],
                    'tagLists'                      => [],
                    'restricts'                     => [],
                    'witnesses'                     => [],
                    'virtuals'                      => [],
                    'embeddedReferenceLinks'        => [],
                    'virtualEmbeddedReferences'     => [],
                    'requirements'                  => [],
                    'storageUrls'                   => [],
                    'dynamicUrls'                   => [],
                    'secures'                       => [],
                    'geopoints'                     => [],
                    'geopointVirtuals'              => [],
                    'exposeRestricts'               => [],
                ],
            ],
            $this->o()->getModels()
        );
    }

    /**
     * @group unit
     */
    public function testAddModelStat()
    {
        $trackersKey = 'stat';

        $class = 'Class';
        $id = 'id1';
        $operation = 'operation';

        $this->o()->addModel($class, ['key' => 'key', 'id' => $id]);
        $this->o()->addModelStat($class, ['on' => $operation, 'key' => 'key']);

        $trackers = $this->o()->getOperationTrackers($operation);

        $this->assertArrayHasKey($trackersKey, $trackers);
        $this->assertArrayHasKey($id, $trackers[$trackersKey]);
    }
}
