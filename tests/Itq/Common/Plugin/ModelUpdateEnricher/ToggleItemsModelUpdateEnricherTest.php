<?php

/*
 * This file is part of the COMMON package.
 *
 * (c) itiQiti SAS <opensource@itiqiti.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tests\Itq\Common\Plugin\ModelUpdateEnricher;

use Itq\Common\Plugin\ModelUpdateEnricher\ToggleItemsModelUpdateEnricher;
use Itq\Common\Tests\Plugin\ModelUpdateEnricher\Base\AbstractModelUpdateEnricherTestCase;

/**
 * @author itiQiti Dev Team <opensource@itiqiti.com>
 *
 * @group plugins
 * @group plugins/models
 * @group plugins/models/update-enrichers
 * @group plugins/models/update-enrichers/toggle-items
 */
class ToggleItemsModelUpdateEnricherTest extends AbstractModelUpdateEnricherTestCase
{
    /**
     * @return ToggleItemsModelUpdateEnricher
     */
    public function e()
    {
        /** @noinspection PhpIncompatibleReturnTypeInspection */

        return parent::e();
    }
}
