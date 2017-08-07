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
 * @Target("METHOD")
 */
final class BusinessRule extends Base\AbstractAnnotation
{
    /**
     * @var string
     */
    public $id = null;
    /**
     * @var string
     */
    public $name = null;
    /**
     * @var string
     */
    public $model = null;
    /**
     * @var string
     */
    public $operation = null;
    /**
     * @var string
     */
    public $when = 'before';
    /**
     * @var array
     */
    public $tenant = null;
}
