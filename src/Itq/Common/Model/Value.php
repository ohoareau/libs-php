<?php

/*
 * This file is part of the COMMON package.
 *
 * (c) itiQiti SAS <opensource@itiqiti.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Itq\Common\Model;

use Itq\Common\Model\Base\AbstractModel;
use /** @noinspection PhpUnusedAliasInspection */ JMS\Serializer\Annotation as Jms;

/**
 * @author itiQiti Dev Team <opensource@itiqiti.com>
 *
 * @Jms\ExclusionPolicy("all")
 * @Jms\AccessorOrder("alphabetical")
 */
class Value extends AbstractModel
{
    /**
     * @var mixed
     */
    public $value;
}
