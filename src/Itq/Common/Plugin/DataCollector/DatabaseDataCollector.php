<?php

/*
 * This file is part of the COMMON package.
 *
 * (c) itiQiti SAS <opensource@itiqiti.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Itq\Common\Plugin\DataCollector;

use Exception;
use Itq\Common\Event;
use Itq\Common\Traits;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * @author itiQiti Dev Team <opensource@itiqiti.com>
 */
class DatabaseDataCollector extends Base\AbstractDataCollector
{
    use Traits\ParameterAware\DebugParameterAwareTrait;
    /**
     * @var array
     */
    protected $data = ['queries' => []];
    /**
     * @param bool $debug
     */
    public function __construct($debug)
    {
        $this->setDebug($debug);
    }
    /**
     * @return array
     */
    public function getQueries()
    {
        return $this->data['queries'];
    }
    /**
     * @param Event\DatabaseQueryEvent $event
     */
    public function onDatabaseQueryExecuted(Event\DatabaseQueryEvent $event)
    {
        if (!$this->isDebug()) {
            return;
        }

        $this->data['queries'][] = [
            'exception' => null !== $event->getException() ? ['code' => $event->getException()->getCode(), 'message' => $event->getException()->getMessage()] : null,
            'params'    => $event->getParams(),
            'query'     => $event->getQuery(),
            'result'    => $event->getResult() instanceof \Iterator ? iterator_to_array($event->getResult()) : $event->getResult(),
            'type'      => $event->getType(),
            'endDate'   => $event->getEndTime(),
            'startDate' => $event->getStartTime(),
            'duration'  => $event->getEndTime() - $event->getStartTime(),
        ];
    }
    /**
     * @param Request        $request
     * @param Response       $response
     * @param Exception|null $exception
     */
    public function collect(Request $request, Response $response, Exception $exception = null)
    {
    }
    /**
     * @return string
     */
    public function getName()
    {
        return 'app_database';
    }
}
