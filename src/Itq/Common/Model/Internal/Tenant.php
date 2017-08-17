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

use JMS\Serializer\Annotation as Jms;
use Itq\Common\Model\Base\AbstractModel;
use Symfony\Component\Validator\Constraints as Assert;
use /** @noinspection PhpUnusedAliasInspection */ Itq\Common\Annotation;

/**
 * @author itiQiti Dev Team <opensource@itiqiti.com>
 *
 * @Jms\ExclusionPolicy("all")
 * @Jms\AccessorOrder("alphabetical")
 * @Annotation\Model("tenant")
 */
class Tenant extends AbstractModel
{
    /**
     * @var string
     *
     * @Jms\Expose
     * @Jms\Groups({"created", "updated", "listed", "detailed"})
     * @Jms\Type("string")
     * @Annotation\Id
     */
    public $id;
    /**
     * @var string
     *
     * @Jms\Expose
     * @Jms\Groups({"created", "updated", "listed", "detailed"})
     * @Jms\Type("string")
     */
    public $name;
}
