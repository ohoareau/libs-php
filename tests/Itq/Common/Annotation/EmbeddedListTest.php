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

use Itq\Common\Annotation\EmbeddedList;
use Itq\Common\Tests\Annotation\Base\AbstractAnnotationTestCase;

/**
 * @author itiQiti Dev Team <opensource@itiqiti.com>
 *
 * @group annotations
 * @group annotations/embedded-list
 */
class EmbeddedListTest extends AbstractAnnotationTestCase
{
    /**
     * @return EmbeddedList
     */
    public function a()
    {
        /** @noinspection PhpIncompatibleReturnTypeInspection */

        return parent::a();
    }
}
