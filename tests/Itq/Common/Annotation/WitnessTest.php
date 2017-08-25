<?php

/*
 * This file is part of the COMMON package.
 *
 * (c) itiQiti SAS <opensource@itiqiti.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tests\Itq\Common\Annotation;

use Itq\Common\Annotation\Witness;
use Itq\Common\Tests\Annotation\Base\AbstractAnnotationTestCase;

/**
 * @author itiQiti Dev Team <opensource@itiqiti.com>
 *
 * @group annotations
 * @group annotations/witness
 */
class WitnessTest extends AbstractAnnotationTestCase
{
    /**
     * @return Witness
     */
    public function a()
    {
        /** @noinspection PhpIncompatibleReturnTypeInspection */

        return parent::a();
    }
}
