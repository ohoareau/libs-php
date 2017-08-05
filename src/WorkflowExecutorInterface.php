<?php

/*
 * This file is part of the COMMON package.
 *
 * (c) itiQiti SAS <cto@itiqiti.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Itq\Common;

/**
 * Workflow Executor Interface.
 *
 * @author itiQiti Dev Team <opensource@itiqiti.com>
 */
interface WorkflowExecutorInterface
{
    /**
     * @param string $modelName
     * @param string $operation
     * @param mixed  $model
     * @param array  $options
     *
     * @return $this
     */
    public function executeModelOperation($modelName, $operation, $model, array $options = []);
}
