<?php

/*
 * This file is part of the COMMON package.
 *
 * (c) itiQiti SAS <opensource@itiqiti.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Itq\Common\Traits;

/**
 * Base trait.
 *
 * @author itiQiti Dev Team <opensource@itiqiti.com>
 */
trait BaseTrait
{
    use ObjectTrait;
    use PhpEngineTrait;
    use InjectionTrait;
    use ExceptionThrowerTrait;
    use MissingMethodCatcherTrait;
    use Helper\String\StringTrait;
    use Helper\Collection\CollectionTrait;
}
