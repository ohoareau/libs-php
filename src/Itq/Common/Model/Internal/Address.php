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

use Itq\Common\Traits;
use Itq\Common\Model\Base\AbstractModel;
use /** @noinspection PhpUnusedAliasInspection */ Itq\Common\Annotation;
use /** @noinspection PhpUnusedAliasInspection */ JMS\Serializer\Annotation as Jms;

/**
 * @author itiQiti Dev Team <opensource@itiqiti.com>
 *
 * @Annotation\Model("address")
 *
 * @Jms\ExclusionPolicy("all")
 * @Jms\AccessorOrder("alphabetical")
 */
class Address extends AbstractModel
{
    use Traits\PopulatorTrait;
    /**
     * @var string
     *
     * @Jms\Expose
     * @Jms\Type("string")
     * @Jms\Groups({"detailed"})
     */
    public $type;
    /**
     * @var string
     *
     * @Jms\Expose
     * @Jms\Type("string")
     * @Jms\Groups({"detailed"})
     */
    public $location;
    /**
     * @var string
     *
     * @Jms\Expose
     * @Jms\Type("string")
     * @Jms\Groups({"detailed"})
     */
    public $street;
    /**
     * @var string
     *
     * @Jms\Expose
     * @Jms\Type("string")
     * @Jms\Groups({"detailed"})
     */
    public $complement;
    /**
     * @var string
     *
     * @Jms\Expose
     * @Jms\Type("string")
     * @Jms\Groups({"detailed"})
     */
    public $city;
    /**
     * @var string
     *
     * @Jms\Expose
     * @Jms\Type("string")
     * @Jms\Groups({"detailed"})
     */
    public $zipCode;
    /**
     * @var double
     *
     * @Jms\Expose
     * @Jms\Type("float")
     * @Jms\groups({"detailed"})
     */
    public $latitude;
    /**
     * @var double
     *
     * @Jms\Expose
     * @Jms\Type("float")
     * @Jms\groups({"detailed"})
     */
    public $longitude;
    /**
     * @param array $data
     */
    public function __construct(array $data = [])
    {
        $this->populate($data);
    }
}
