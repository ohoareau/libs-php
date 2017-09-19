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
use Itq\Common\InstanceProviderInterface;
use Itq\Common\Aware\InstanceChangeAwareInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * Instance Service.
 *
 * @author itiQiti Dev Team <opensource@itiqiti.com>
 */
class InstanceService implements InstanceProviderInterface
{
    use Traits\ServiceTrait;
    use Traits\ServiceAware\MigrationServiceAwareTrait;
    /**
     * @param EventDispatcherInterface $eventDispatcher
     * @param MigrationService         $migrationService
     * @param array                    $allowedIdPatterns
     */
    public function __construct(
        EventDispatcherInterface $eventDispatcher,
        MigrationService $migrationService,
        array $allowedIdPatterns = []
    ) {
        $this->setEventDispatcher($eventDispatcher);
        $this->setMigrationService($migrationService);
        $this->setAllowedIdPatterns($allowedIdPatterns);
    }
    /**
     * @param InstanceChangeAwareInterface $instanceChangeAware
     *
     * @return $this
     */
    public function addInstanceChangeAware(InstanceChangeAwareInterface $instanceChangeAware)
    {
        return $this->pushArrayParameterItem('instanceChangeAwares', $instanceChangeAware);
    }
    /**
     * @return InstanceChangeAwareInterface[]
     */
    public function getInstanceChangeAwares()
    {
        return $this->getArrayParameter('instanceChangeAwares');
    }
    /**
     * @param array $allowedIdPatterns
     *
     * @return $this
     */
    public function setAllowedIdPatterns(array $allowedIdPatterns)
    {
        return $this->setParameter('allowedIdPatterns', (array) $allowedIdPatterns);
    }
    /**
     * @return string[]
     */
    public function getAllowedIdPatterns()
    {
        return $this->getArrayParameter('allowedIdPatterns');
    }
    /**
     * @param string $id
     *
     * @return bool
     */
    public function isAllowedId($id)
    {
        $result = false;

        foreach ($this->getAllowedIdPatterns() as $allowedIdPattern) {
            if (0 < preg_match($allowedIdPattern, $id)) {
                $result = true;
                break;
            }
        }

        return $result;
    }
    /**
     * @param string $id
     *
     * @return $this
     *
     * @throws Exception
     */
    public function checkAllowedId($id)
    {
        if (!$this->isAllowedId($id)) {
            throw $this->createDeniedException('instance.id.pattern.denied', $id);
        }

        return $this;
    }
    /**
     * @param string $id
     * @param array  $options
     *
     * @return object
     */
    public function load($id, array $options = [])
    {
        $instance = $this->instantiate(['id' => $id]);

        foreach ($this->getInstanceChangeAwares() as $instanceChangeAware) {
            $instanceChangeAware->changeInstance($instance, $options);
        }

        $this->dispatch('instance.changed', ['instance' => $instance, 'options' => $options]);

        return $instance;
    }
    /**
     * @param array $data
     * @param array $options
     *
     * @return object
     */
    public function create(array $data, array $options = [])
    {
        $instance = $this->instantiate($data);

        $this->initializeInstance($instance, $options);
        $this->dispatch('instance.created', ['instance' => $instance, 'options' => $options]);

        return $instance;
    }
    /**
     * @param Model\Internal\Instance $instance
     * @param array                   $options
     *
     * @return $this
     */
    protected function initializeInstance(Model\Internal\Instance $instance, array $options)
    {
        foreach ($this->getInstanceChangeAwares() as $instanceChangeAware) {
            $instanceChangeAware->changeInstance($instance, $options);
        }

        $this->getMigrationService()->upgrade();

        foreach ($this->getInstanceChangeAwares() as $instanceChangeAware) {
            $instanceChangeAware->changeInstanceToDefault($options);
        }

        return $this;
    }
    /**
     * @param array $data
     *
     * @return Model\Internal\Instance
     */
    protected function instantiate(array $data)
    {
        $data += ['id' => null];

        $this->checkAllowedId($data['id']);

        return new Model\Internal\Instance($data);
    }
}
