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

use Itq\Common\PreprocessableClassFinder;
use Itq\Common\Service\PreprocessorService;
use Itq\Common\Tests\Service\Base\AbstractServiceTestCase;

use Doctrine\Common\Annotations\AnnotationReader;

/**
 * @author itiQiti Dev Team <opensource@itiqiti.com>
 *
 * @group services
 * @group services/preprocessor
 */
class PreprocessorServiceTest extends AbstractServiceTestCase
{
    /**
     * @return PreprocessorService
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
        $ar = new AnnotationReader();

        return [$ar, new PreprocessableClassFinder($ar)];
    }
}
