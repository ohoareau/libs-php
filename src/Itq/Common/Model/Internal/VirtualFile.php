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

use Itq\Common\Model\Base\AbstractModel;
use /** @noinspection PhpUnusedAliasInspection */ Itq\Common\Annotation;
use /** @noinspection PhpUnusedAliasInspection */ JMS\Serializer\Annotation as Jms;

/**
 * @author itiQiti Dev Team <opensource@itiqiti.com>
 *
 * @Jms\ExclusionPolicy("all")
 * @Jms\AccessorOrder("alphabetical")
 * @Annotation\Model("virtualFile")
 */
class VirtualFile extends AbstractModel
{
    /**
     * @var string
     * @Jms\Expose
     * @Jms\Type("string")
     * @Jms\Groups({"created", "updated", "listed", "detailed"})
     */
    public $content;
    /**
     * @var string
     * @Jms\Expose
     * @Jms\Type("string")
     * @Jms\Groups({"created", "updated", "listed", "detailed"})
     */
    public $contentType;
    /**
     * @var string
     * @Jms\Expose
     * @Jms\Type("string")
     * @Jms\Groups({"created", "updated", "listed", "detailed"})
     */
    public $fileName;
    /**
     * @var int
     * @Jms\Expose
     * @Jms\Type("integer")
     * @Jms\Groups({"created", "updated", "listed", "detailed"})
     */
    public $cacheTtl;
    /**
     * @var bool
     * @Jms\Expose
     * @Jms\Type("boolean")
     * @Jms\Groups({"created", "updated", "listed", "detailed"})
     */
    public $sensitive;
    /**
     * @param string|null $content
     * @param string|null $contentType
     * @param string|null $fileName
     * @param bool|null   $sensitive
     * @param int|null    $cacheTtl
     */
    public function __construct($content = null, $contentType = null, $fileName = null, $sensitive = null, $cacheTtl = null)
    {
        $this->content     = $content;
        $this->contentType = $contentType;
        $this->fileName    = $fileName;
        $this->sensitive   = $sensitive;
        $this->cacheTtl    = $cacheTtl;
    }
}
