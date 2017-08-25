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

use Itq\Common\Annotation\Id;
use Itq\Common\Tests\Annotation\Base\AbstractAnnotationTestCase;

/**
 * @author itiQiti Dev Team <opensource@itiqiti.com>
 *
 * @group annotations
 * @group annotations/id
 */
class IdTest extends AbstractAnnotationTestCase
{
    /**
     * @return Id
     */
    public function a()
    {
        /** @noinspection PhpIncompatibleReturnTypeInspection */

        return parent::a();
    }
}
