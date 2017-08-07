<?php

/*
 * This file is part of the COMMON package.
 *
 * (c) itiQiti SAS <opensource@itiqiti.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tests\Itq\Common\Model;

use /** @noinspection PhpUnusedAliasInspection */  Itq\Common\Annotation;

/**
 * @author itiQiti Dev Team <opensource@itiqiti.com>
 *
 * @Annotation\Model("modelWithAnnotatedProperties")
 */
class ModelWithAnnotatedProperties
{
    /**
     * @Annotation\EmbeddedReference
     */
    public $embeddedReference;
    /**
     * @Annotation\Id
     */
    public $id;
}
