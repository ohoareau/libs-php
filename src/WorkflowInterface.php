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
 * Workflow Interface.
 *
 * @author itiQiti Dev Team <opensource@itiqiti.com>
 */
interface WorkflowInterface
{
    /**
     * @param string $currentStep
     * @param string $targetStep
     *
     * @return bool
     */
    public function hasTransition($currentStep, $targetStep);
    /**
     * @param string $transition
     *
     * @return array
     */
    public function getTransitionAliases($transition);
}
