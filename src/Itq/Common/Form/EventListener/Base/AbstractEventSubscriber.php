<?php

/*
 * This file is part of the COMMON package.
 *
 * (c) itiQiti SAS <opensource@itiqiti.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Itq\Common\Form\EventListener\Base;

use Itq\Common\Traits;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * @author itiQiti Dev Team <opensource@itiqiti.com>
 */
abstract class AbstractEventSubscriber implements EventSubscriberInterface
{
    use Traits\BaseTrait;
}
