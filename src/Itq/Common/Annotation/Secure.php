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
final class Secure extends Base\AbstractAnnotation
{
    /**
     * @var string
     */
    public $operation = 'all';
    /**
     * @var null|string
     */
    public $role = null;
    /**
     * @var bool
     */
    public $allow = true;
    /**
     * @var bool
     */
    public $silent = true;
    /**
     * @var null|string
     */
    public $message = null;
}
