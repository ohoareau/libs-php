<?php

/*
 * This file is part of the COMMON package.
 *
 * (c) itiQiti SAS <opensource@itiqiti.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Itq\Common\Plugin\Action;

use Itq\Common\Bag;
use Itq\Common\Model;
use Itq\Common\Traits;
use Itq\Common\Service;
use Itq\Common\Plugin\Base\AbstractPlugin;
use /** @noinspection PhpUnusedAliasInspection */ Itq\Common\Annotation;

/**
 * @author itiQiti Dev Team <opensource@itiqiti.com>
 */
class TaskAction extends AbstractPlugin
{
    use Traits\ServiceAware\CrudServiceAwareTrait;
    use Traits\ServiceAware\TaskServiceAwareTrait;
    /**
     * @param Service\TaskService $taskService
     * @param Service\CrudService $crudService
     */
    public function __construct(Service\TaskService $taskService, Service\CrudService $crudService)
    {
        $this->setTaskService($taskService);
        $this->setCrudService($crudService);
    }
    /**
     * @param Bag $params
     * @param Bag $context
     *
     * @Annotation\Action("task", description="execute a task")
     *
     * @throws \Exception
     */
    public function executeTask(Bag $params, Bag $context)
    {
        if ($params->has('foreach')) {
            $foreach = $params->get('foreach');
            if (!is_array($foreach)) {
                $foreach = ['type' => $foreach];
            }
            if (!isset($foreach['type'])) {
                throw $this->createRequiredException("Missing type for task foreach");
            }
            $foreach += ['fields' => [], 'sorts' => [], 'limit' => null, 'offset' => 0, 'criteria' => [], 'options' => []];

            $items = $this->getForeachItems(
                $foreach['type'],
                $foreach['criteria'],
                $foreach['fields'],
                $foreach['limit'],
                $foreach['offset'],
                $foreach['sorts'],
                $foreach['options'],
                $params,
                $context
            );
        } else {
            $items = [[$params, $context]];
        }

        foreach ($items as $k => $item) {
            /** @var Bag $_params */
            /** @var Bag $_context */
            list ($_params, $_context) = $item;
            try {
                $results = $this->getTaskService()->execute($_params->get('name'), $_params->get('params', []) + $_params->all());
                if (is_array($results)) {
                    $context->set($results);
                }
            } catch (\Exception $e) {
                if (false === $_context->get('throwException', true)) {
                    $this->log('exception', $_params->get('name')."($k) : ".$e->getMessage(), [], ['exception' => $e]);
                    continue;
                }

                throw $e;
            }
        }
    }
    /**
     * @param string $type
     * @param string $message
     * @param array  $params
     * @param array  $options
     */
    protected function log($type, $message, /** @noinspection PhpUnusedParameterInspection */ $params = [], $options = [])
    {
        /** @var \DateTime $now */
        $now = date_create();

        switch ($type) {
            case 'exception':
                /** @var \Exception $exception */
                $exception = $options['exception'];
                echo sprintf('[%s] EXCEPTION #%d: %s', $now->format('c'), $exception->getCode(), $message)."\n";
                break;
            default:
                echo sprintf('[%s] %s: %s', $now->format('c'), strtoupper($type), $message)."\n";
                break;
        }
    }
    /**
     * @param string $type
     * @param array  $criteria
     * @param array  $fields
     * @param null   $limit
     * @param int    $offset
     * @param array  $sorts
     * @param array  $options
     * @param Bag    $params
     * @param Bag    $context
     *
     * @return array
     */
    protected function getForeachItems($type, $criteria, $fields, $limit, $offset, $sorts, $options, Bag $params, Bag $context)
    {
        $items       = [];
        $variableKey = $type;
        $docs        = $this->getCrudService()->get($type)->find($criteria, array_fill_keys($fields, true) + ['id' => true], $limit, $offset, $sorts, $options);

        foreach ($docs as $k => $doc) {
            $_params = clone $params;
            $_params->set($variableKey, $doc);
            $items[$k] = [$_params, $context];
        }

        return $items;
    }
}
