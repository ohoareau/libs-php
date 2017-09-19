<?php

/*
 * This file is part of the COMMON package.
 *
 * (c) itiQiti SAS <opensource@itiqiti.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Itq\Common\Model\Internal;

use Itq\Common\Model\Base\AbstractBasicModel;
use /** @noinspection PhpUnusedAliasInspection */ JMS\Serializer\Annotation as Jms;

/**
 * @author itiQiti Dev Team <opensource@itiqiti.com>
 *
 * @Jms\ExclusionPolicy("all")
 * @Jms\AccessorOrder("alphabetical")
 */
class Instance extends AbstractBasicModel
{
    /**
     * @var string
     *
     * @Jms\Expose
     * @Jms\Type("string")
     * @Jms\Groups({"created", "listed", "detailed"})
     */
    public $id;
}
