<?php

/*
 * This file is part of the COMMON package.
 *
 * (c) itiQiti SAS <opensource@itiqiti.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Itq\Common\Service;

use DateTimeZone;
use Itq\Common\Traits;
use Itq\Common\Adapter;

/**
 * System Service.
 *
 * @author itiQiti Dev Team <opensource@itiqiti.com>
 */
class SystemService
{
    use Traits\ServiceTrait;
    use Traits\AdapterAware\SystemAdapterAwareTrait;
    /**
     * @param Adapter\SystemAdapterInterface $adapter
     */
    public function __construct(Adapter\SystemAdapterInterface $adapter)
    {
        $this->setSystemAdapter($adapter);
    }
    /**
     * @return string
     */
    public function getTempDirectory()
    {
        return $this->getSystemAdapter()->getTempDirectory();
    }
    /**
     * @param string $command
     *
     * @return string
     *
     * @throws \Exception
     */
    public function execute($command)
    {
        $output = [];
        $return = 0;

        $this->getSystemAdapter()->exec($command, $output, $return);

        if (0 !== $return) {
            throw $this->createFailedException('Command [%s] failed with error code [%d]', $command, $return);
        }

        return join(PHP_EOL, $output);
    }
    /**
     * @param string $command
     * @param array  $options
     *
     * @return $this
     *
     * @throws \Exception
     */
    public function executeInForeground($command, array $options = [])
    {
        unset($options);

        $return = 0;

        $this->getSystemAdapter()->passthru($command, $return);

        if (0 !== $return) {
            throw $this->createFailedException('Foreground command [%s] failed with error code [%d]', $command, $return);
        }

        return $this;
    }
    /**
     * @return float
     */
    public function getCurrentTime()
    {
        return $this->getSystemAdapter()->microtime() + $this->getCurrentTimeOffset();
    }
    /**
     * @return DateTimeZone
     */
    public function getCurrentTimeZone()
    {
        if (!$this->hasParameter('currentTimeZone')) {
            return $this->getDefaultTimeZone();
        }

        return $this->getParameter('currentTimeZone');
    }
    /**
     * @return DateTimeZone
     */
    public function getDefaultTimeZone()
    {
        if (!$this->hasParameter('defaultTimeZone')) {
            $this->setParameter('defaultTimeZone', new DateTimeZone($this->getSystemAdapter()->getTimeZone()));
        }

        return $this->getParameter('defaultTimeZone');
    }
    /**
     * @param DateTimeZone $timeZone
     *
     * @return $this
     */
    public function setCurrentTimeZone(DateTimeZone $timeZone)
    {
        return $this->setParameter('currentTimeZone', $timeZone);
    }
    /**
     * @return int
     */
    public function getCurrentTimeOffset()
    {
        return $this->getParameterIfExists('offset', 0);
    }
    /**
     * @param int $time
     *
     * @return $this
     */
    public function setCurrentTime($time)
    {
        return $this->setParameter('offset', (int) ($time - $this->getSystemAdapter()->microtime()));
    }
    /**
     * @return $this
     */
    public function resetCurrentTime()
    {
        return $this->unsetParameter('offset');
    }
    /**
     * @return $this
     */
    public function resetCurrentTimeZone()
    {
        return $this->unsetParameter('currentTimeZone');
    }
    /**
     * @return string
     */
    public function getHostName()
    {
        return $this->getSystemAdapter()->hostname();
    }
}
