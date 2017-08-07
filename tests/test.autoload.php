<?php

/*
 * This file is part of the COMMON package.
 *
 * (c) itiQiti SAS <opensource@itiqiti.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/** @var \Composer\Autoload\ClassLoader $loader */
$loader = require __DIR__.'/../vendor/autoload.php';

\Doctrine\Common\Annotations\AnnotationRegistry::registerLoader(array($loader, 'loadClass'));
\Doctrine\Common\Annotations\AnnotationReader::addGlobalIgnoredName('group');
\Doctrine\Common\Annotations\AnnotationReader::addGlobalIgnoredName('dataProvider');

return $loader;
