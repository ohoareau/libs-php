<?php

/*
 * This file is part of the COMMON package.
 *
 * (c) itiQiti SAS <opensource@itiqiti.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Itq\Common\Plugin\Poller\Base;

use Itq\Common\Plugin\PollerInterface;
use Itq\Common\Plugin\Base\AbstractPlugin;
use Itq\Common\Plugin\PollableSourceInterface;

/**
 * @author itiQiti Dev Team <opensource@itiqiti.com>
 */
abstract class AbstractPoller extends AbstractPlugin implements PollerInterface
{
    /**
     * @param array $options
     */
    public function __construct(array $options = [])
    {
        $options += ['timeout' => null];

        $this->setOptions($options);
    }
    /**
     * @param string                  $name
     * @param PollableSourceInterface $source
     */
    public function add($name, PollableSourceInterface $source)
    {
        $this->setArrayParameterKey('sources', $name, $source);
    }
    /**
     * @return PollableSourceInterface[]
     */
    public function all()
    {
        return $this->getArrayParameter('sources');
    }
    /**
     * @param array $options
     *
     * @return $this
     */
    public function setOptions(array $options)
    {
        return $this->setParameter('options', $options);
    }
    /**
     * @return array
     */
    public function getOptions()
    {
        return $this->getArrayParameter('options');
    }
    /**
     * @param string     $name
     * @param mixed|null $default
     *
     * @return mixed
     */
    public function getOption($name, $default = null)
    {
        return $this->getArrayParameterKeyIfExists('options', $name, $default);
    }
}
