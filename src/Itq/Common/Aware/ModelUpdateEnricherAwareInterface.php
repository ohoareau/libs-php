<?php

/*
 * This file is part of the COMMON package.
 *
 * (c) itiQiti SAS <opensource@itiqiti.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Itq\Common\Aware;

use Itq\Common\Plugin;

/**
 * @author itiQiti Dev Team <opensource@itiqiti.com>
 */
interface ModelUpdateEnricherAwareInterface
{
    /**
     * @param string                              $type
     * @param Plugin\ModelUpdateEnricherInterface $updateEnricher
     *
     * @return $this
     */
    public function addModelUpdateEnricher($type, Plugin\ModelUpdateEnricherInterface $updateEnricher);
    /**
     * @return Plugin\ModelUpdateEnricherInterface[]
     */
    public function getModelUpdateEnrichers();
    /**
     * @param string $type
     *
     * @return Plugin\ModelUpdateEnricherInterface
     */
    public function getModelUpdateEnricher($type);
}
