<?php

/*
 * This file is part of the COMMON package.
 *
 * (c) itiQiti SAS <opensource@itiqiti.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Itq\Common\Iterator;

use Closure;

/**
 * @author itiQiti Dev Team <opensource@itiqiti.com>
 */
class ChunkedIterator extends Base\AbstractIterator
{
    /**
     * @var Closure
     */
    protected $fetcher;
    /**
     * @var int
     */
    protected $chunkSize;
    /**
     * @var int|null
     */
    protected $limit;
    /**
     * @var int
     */
    protected $loops;
    /**
     * @var int
     */
    protected $position;
    /**
     * @var array
     */
    protected $lastLoopResults;
    /**
     * @var bool
     */
    protected $nextIsFirstLoop;
    /**
     * @var int
     */
    protected $lastLoopResultCount;
    /**
     * @var int
     */
    protected $lastLoopId;
    /**
     * @var int
     */
    protected $lastLoopLimit;
    /**
     * @var bool
     */
    protected $satisfied;
    /**
     * @var int
     */
    protected $remaining;
    /**
     * @var Closure
     */
    protected $itemCallback;
    /**
     * @param Closure      $fetcher
     * @param int          $chunkSize
     * @param null|int     $limit
     * @param Closure|null $itemCallback
     */
    public function __construct(Closure $fetcher, $chunkSize, $limit = null, Closure $itemCallback = null)
    {
        $this->fetcher      = $fetcher;
        $this->chunkSize    = $chunkSize;
        $this->limit        = $limit;
        $this->itemCallback = $itemCallback;

        $this->rewind();
    }
    /**
     * @return int
     */
    public function getCurrentLoopCount()
    {
        return $this->loops;
    }
    /**
     * @return mixed
     */
    public function current()
    {
        return $this->lastLoopResults;
    }
    /**
     * @return void
     */
    public function next()
    {
        if ($this->limit > 0) {
            $this->remaining = ($this->limit > $this->position) ? ($this->limit - $this->position) : 0;
            if ($this->remaining <= 0) {
                $this->satisfied = true;
            }
        }
        if ($this->satisfied) {
            return;
        }

        $fetcher               = $this->fetcher;
        $n                     = ($this->limit > 0 && $this->limit < $this->chunkSize) ? $this->limit : $this->chunkSize;
        $this->lastLoopLimit   = (null === $this->remaining || $n < $this->remaining) ? $n : $this->remaining;
        $this->lastLoopResults = $fetcher($this->lastLoopLimit, $this->position);
        $itemCallback          = $this->itemCallback;

        if ($this->lastLoopResults instanceof \Traversable) {
            $iterator = $this->lastLoopResults;
            $this->lastLoopResults = [];
            foreach ($iterator as $k => $v) {
                if ($itemCallback) {
                    $v = $itemCallback($v, $k);
                }
                $this->lastLoopResults[$k] = $v;
            }
            unset($iterator);
        } elseif ($itemCallback) {
            foreach ($this->lastLoopResults as $k => $v) {
                $this->lastLoopResults[$k] = $itemCallback($v, $k);
            }
        }
        $this->lastLoopResultCount = count($this->lastLoopResults);
        $this->position           += $this->lastLoopResultCount;
        $this->nextIsFirstLoop     = false;
        $this->lastLoopId          = $this->loops;
        $this->loops++;
    }
    /**
     * @return mixed
     */
    public function key()
    {
        return $this->lastLoopId;
    }
    /**
     * @return bool
     */
    public function valid()
    {
        if ($this->nextIsFirstLoop) {
            $this->next();
        }

        $v = !$this->satisfied && $this->lastLoopResultCount > 0;

        if (null === $this->limit && ($this->lastLoopResultCount < $this->lastLoopLimit)) {
            $this->satisfied = true;
        }

        return $v;
    }
    /**
     *
     */
    public function rewind()
    {
        $this->position            = 0;
        $this->loops               = 0;
        $this->lastLoopResults     = [];
        $this->nextIsFirstLoop     = true;
        $this->lastLoopResultCount = 0;
        $this->lastLoopId          = 0;
        $this->lastLoopLimit       = null;
        $this->satisfied           = false;
        $this->remaining           = $this->limit > 0 ? $this->limit : null;
    }
}
