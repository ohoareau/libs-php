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

use Exception;
use Itq\Common\Model;
use Itq\Common\Traits;
use Itq\Common\NotificationModeProviderInterface;
use Itq\Common\Aware\NotificationModeChangeAwareInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * NotificationMode Service.
 *
 * @author itiQiti Dev Team <opensource@itiqiti.com>
 */
class NotificationModeService implements NotificationModeProviderInterface
{
    use Traits\ServiceTrait;
    /**
     * @param EventDispatcherInterface $eventDispatcher
     * @param array                    $modes
     */
    public function __construct(EventDispatcherInterface $eventDispatcher, array $modes = [])
    {
        $this->setEventDispatcher($eventDispatcher);
        $this->setModes($modes);
    }
    /**
     * @param string $id
     * @param bool   $enabled
     *
     * @return $this
     */
    public function addMode($id, $enabled)
    {
        return $this->setArrayParameterKey('modes', $id, true === $enabled);
    }
    /**
     * @param array $modes
     *
     * @return $this
     */
    public function setModes(array $modes)
    {
        $this->setParameter('modes', []);

        foreach ($modes as $mode => $enabled) {
            $this->addMode($mode, $enabled);
        }

        return $this;
    }
    /**
     * @return bool[]
     */
    public function getModes()
    {
        return $this->getArrayParameter('modes');
    }
    /**
     * @param NotificationModeChangeAwareInterface $notificationModeChangeAware
     *
     * @return $this
     */
    public function addNotificationModeChangeAware(NotificationModeChangeAwareInterface $notificationModeChangeAware)
    {
        return $this->pushArrayParameterItem('notificationModeChangeAwares', $notificationModeChangeAware);
    }
    /**
     * @return NotificationModeChangeAwareInterface[]
     */
    public function getNotificationModeChangeAwares()
    {
        return $this->getArrayParameter('notificationModeChangeAwares');
    }
    /**
     * @param string $id
     *
     * @return $this
     */
    public function setCurrentMode($id)
    {
        $notificationMode = $this->instantiate(['id' => $id]);

        foreach ($this->getNotificationModeChangeAwares() as $notificationModeChangeAware) {
            $notificationModeChangeAware->changeNotificationMode($notificationMode);
        }

        $this->dispatch('notificationmode.changed', ['notificationMode' => $notificationMode]);

        return $this;
    }
    /**
     * @param string $id
     *
     * @return $this
     *
     * @throws Exception
     */
    public function checkMode($id)
    {
        if (!$this->hasMode($id)) {
            throw $this->createNotFoundException('notificationmode.unknown', ['id' => $id]);
        }

        return $this;
    }
    /**
     * @param string $id
     *
     * @return bool
     */
    public function hasMode($id)
    {
        return true === $this->getArrayParameterKeyIfExists('modes', $id, false);
    }
    /**
     * @param array $data
     *
     * @return Model\Internal\NotificationMode
     */
    protected function instantiate(array $data)
    {
        $data += ['id' => null];

        $i = new Model\Internal\NotificationMode();

        foreach ($data as $k => $v) {
            if (!property_exists($i, $k)) {
                continue;
            }
            $i->$k = $v;
        }

        return $i;
    }
}
