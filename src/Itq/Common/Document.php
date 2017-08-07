<?php

/*
 * This file is part of the COMMON package.
 *
 * (c) itiQiti SAS <opensource@itiqiti.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Itq\Common;

/**
 * @author itiQiti Dev Team <opensource@itiqiti.com>
 */
class Document implements DocumentInterface
{
    /**
     * @var mixed
     */
    protected $content;
    /**
     * @var string
     */
    protected $contentType;
    /**
     * @var string
     */
    protected $fileName;
    /**
     * @param mixed  $content
     * @param string $contentType
     * @param string $fileName
     */
    public function __construct($content, $contentType, $fileName)
    {
        $this->setContent($content);
        $this->setContentType($contentType);
        $this->setFileName($fileName);
    }
    /**
     * @return mixed
     */
    public function getContent()
    {
        return $this->content;
    }
    /**
     * @return string
     */
    public function getContentType()
    {
        return $this->contentType;
    }
    /**
     * @return string
     */
    public function getFileName()
    {
        return $this->fileName;
    }
    /**
     * @param mixed $content
     *
     * @return $this
     */
    protected function setContent($content)
    {
        $this->content = $content;

        return $this;
    }
    /**
     * @param string $contentType
     *
     * @return $this
     */
    protected function setContentType($contentType)
    {
        $this->contentType = $contentType;

        return $this;
    }
    /**
     * @param string $fileName
     *
     * @return $this
     */
    protected function setFileName($fileName)
    {
        $this->fileName = $fileName;

        return $this;
    }
}
