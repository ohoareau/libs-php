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
use Itq\Common\Model\Base\AbstractBasicModel;

/**
 * @author itiQiti Dev Team <opensource@itiqiti.com>
 *
 * @Jms\ExclusionPolicy("all")
 * @Jms\AccessorOrder("alphabetical")
 */
class ImportResult extends AbstractBasicModel
{
    /**
     * @var array
     *
     * @Jms\Expose
     * @Jms\Type("array")
     * @Jms\Groups({"imported"})
     */
    public $statuses = [];
    /**
     * @var array
     *
     * @Jms\Expose
     * @Jms\Type("array")
     * @Jms\Groups({"imported"})
     */
    public $items = [];
    /**
     * @var array
     *
     * @Jms\Expose
     * @Jms\Type("array")
     * @Jms\Groups({"imported"})
     */
    public $counts = [];
    /**
     * @var array
     *
     * @Jms\Expose
     * @Jms\Type("array")
     * @Jms\Groups({"imported"})
     */
    public $tops = [];
    /**
     * @var array
     *
     * @Jms\Expose
     * @Jms\Type("array")
     * @Jms\Groups({"imported"})
     */
    public $errors = [];
    /**
     * @var array
     *
     * @Jms\Expose
     * @Jms\Type("array")
     * @Jms\Groups({"imported"})
     */
    public $durations = [];
    /**
     * @var array
     *
     * @Jms\Expose
     * @Jms\Type("array")
     * @Jms\Groups({"imported"})
     */
    public $speeds = [];
    /**
     * @var string
     *
     * @Jms\Expose
     * @Jms\Type("string")
     * @Jms\Groups({"imported"})
     */
    public $progressToken = null;
}
