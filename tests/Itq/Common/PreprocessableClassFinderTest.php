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

use Itq\Common\PreprocessableClassFinder;
use Itq\Common\Tests\Base\AbstractTestCase;

use Doctrine\Common\Annotations\AnnotationReader;

/**
 * @author itiQiti Dev Team <opensource@itiqiti.com>
 *
 * @group services
 * @group services/preprocessor
 */
class PreprocessableClassFinderTest extends AbstractTestCase
{
    /**
     * @return PreprocessableClassFinder
     */
    public function o()
    {
        /** @noinspection PhpIncompatibleReturnTypeInspection */

        return parent::o();
    }
    /**
     * @return array
     */
    public function constructor()
    {
        return [new AnnotationReader()];
    }
    /**
     * @group unit
     */
    public function testIsCompilerAnnotatedClass()
    {
        $this->assertTrue($this->o()->isCompilerAnnotatedClass(Model\Model1::class));
        $this->assertTrue($this->o()->isCompilerAnnotatedClass(Model\Model2::class));
        $this->assertFalse($this->o()->isCompilerAnnotatedClass(__CLASS__));
    }
    /**
     * @group unit
     */
    public function testFindCompilerAnnotatedClassesInDirectory()
    {
        $classes = $this->o()->findCompilerAnnotatedClassesInDirectory(__DIR__.'/Model');

        $expected = [
            Model\Model1::class,
            Model\Model2::class,
            Model\ModelWithAnnotatedProperties::class,
        ];

        $this->assertEquals($expected, $classes);
        $this->assertEquals(count($expected), count($classes));
    }
}
