<?php

/*
 * This file is part of the COMMON package.
 *
 * (c) itiQiti SAS <opensource@itiqiti.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Itq\Common\Service\Model;

use Itq\Common\Aware\ModelDynamicPropertyBuilderAwareInterface;

/**
 * @author itiQiti Dev Team <opensource@itiqiti.com>
 */
interface ModelDynamicPropertyBuilderServiceInterface extends ModelDynamicPropertyBuilderAwareInterface
{
    /**
     * @param object $doc
     * @param array  $requestedFields
     * @param object $ctx
     * @param array  $options
     *
     * @return void
     */
    public function build($doc, $requestedFields, $ctx, array $options = []);
    /**
     * @param string $modelId
     * @param mixed  $doc
     * @param string $requestedField
     * @param object $ctx
     * @param array  $options
     *
     * @return void
     */
    public function buildProperty($modelId, $doc, $requestedField, $ctx, array $options = []);
}
