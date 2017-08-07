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
 * @Target("CLASS")
 */
final class Stat extends Base\AbstractAnnotation
{
    /**
     * @var string
     */
    public $key;
    /**
     * @var string
     */
    public $on;
    /**
     * @var string
     */
    public $match;
    /**
     * @var mixed
     */
    public $increment;
    /**
     * @var mixed
     */
    public $decrement;
    /**
     * @var string
     */
    public $type;
    /**
     * @var string
     */
    public $formula;
}
