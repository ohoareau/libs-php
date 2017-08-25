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

use Itq\Common\Annotation\JobType;
use Itq\Common\Tests\Annotation\Base\AbstractAnnotationTestCase;

/**
 * @author itiQiti Dev Team <opensource@itiqiti.com>
 *
 * @group annotations
 * @group annotations/job-type
 */
class JobTypeTest extends AbstractAnnotationTestCase
{
    /**
     * @return JobType
     */
    public function a()
    {
        /** @noinspection PhpIncompatibleReturnTypeInspection */

        return parent::a();
    }
}
