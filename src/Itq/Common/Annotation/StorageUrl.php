<?php

/*
 * This file is part of the COMMON package.
 *
 * (c) itiQiti SAS <opensource@itiqiti.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Itq\Common\Annotation;

/**
 * @author itiQiti Dev Team <opensource@itiqiti.com>
 *
 * @Annotation
 * @Target("PROPERTY")
 */
final class StorageUrl extends Base\AbstractAnnotation
{
    /**
     * @var null|string
     */
    public $level = null;
    /**
     * @var bool
     */
    public $alias = false;
    /**
     * @var null|string
     */
    public $of = null;
    /**
     * @var null|string
     */
    public $prefix = null;
    /**
     * @var array
     */
    public $vars = [];
    /**
     * @var bool
     */
    public $sensitive = false;
    /**
     * @var null|string
     */
    public $cacheTtl = null;
}
