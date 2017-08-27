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

use Itq\Common\Service\Model\ModelPropertyLinearizerServiceInterface;
use Itq\Common\Tests\Plugin\ModelCleaner\Base\AbstractModelCleanerTestCase;
use Itq\Common\Plugin\ModelCleaner\RefreshEmbeddedReferenceLinksModelCleaner;

/**
 * @author itiQiti Dev Team <opensource@itiqiti.com>
 *
 * @group plugins
 * @group plugins/models
 * @group plugins/models/cleaners
 * @group plugins/models/cleaners/refresh-embedded-reference-links
 */
class RefreshEmbeddedReferenceLinksModelCleanerTest extends AbstractModelCleanerTestCase
{
    /**
     * @return RefreshEmbeddedReferenceLinksModelCleaner
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
        return [
            $this->mockedMetaDataService(),
            $this->mockedCrudService(),
            $this->mocked('modelPropertyLinearizer', ModelPropertyLinearizerServiceInterface::class),
        ];
    }
}
