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
final class ResponseModel extends Base\AbstractAnnotation
{
    /**
     * @var string
     */
    public $type = null;
    /**
     * @var string
     */
    public $group = null;
    /**
     * @var bool
     */
    public $collection = false;
}
