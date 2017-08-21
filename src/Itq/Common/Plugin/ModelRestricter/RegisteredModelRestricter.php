<?php

/*
 * This file is part of the COMMON package.
 *
 * (c) itiQiti SAS <opensource@itiqiti.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Itq\Common\Plugin\ModelRestricter;

use Exception;
use Itq\Common\Traits;
use Itq\Common\Service;
use Itq\Common\ModelInterface;

/**
 * @author itiQiti Dev Team <opensource@itiqiti.com>
 */
class RegisteredModelRestricter extends Base\AbstractModelRestricter
{
    use Traits\ServiceAware\CrudServiceAwareTrait;
    use Traits\ServiceAware\MetaDataServiceAwareTrait;
    use Traits\ServiceAware\ExpressionServiceAwareTrait;
    /**
     * @param Service\MetaDataService   $metaDataService
     * @param Service\CrudService       $crudService
     * @param Service\ExpressionService $expressionService
     */
    public function __construct(
        Service\MetaDataService   $metaDataService,
        Service\CrudService       $crudService,
        Service\ExpressionService $expressionService
    ) {
        $this->setMetaDataService($metaDataService);
        $this->setCrudService($crudService);
        $this->setExpressionService($expressionService);
    }
    /**
     * @param ModelInterface $doc
     * @param array          $options
     *
     * @throws Exception
     */
    public function restrict($doc, array $options = [])
    {
        $operation = isset($options['operation']) ? $options['operation'] : null;

        unset($options);

        $restricts = $this->getMetaDataService()->getModelRestricts($doc);

        if (!count($restricts) || !isset($doc->id)) {
            return;
        }
        $retrievedDoc      = $this->getCrudByModelClass($doc)->get($doc->getId(), ['stats']);
        $selectedRestricts = [];

        if (isset($restricts[$operation])) {
            $selectedRestricts += $restricts[$operation];
        }
        if (isset($doc->status) && isset($restricts['status.'.$doc->status])) {
            $selectedRestricts += $restricts['status.'.$doc->status];
        }

        foreach ($selectedRestricts as $restrict) {
            $negate    = false;
            $condition = 'false';
            if (isset($restrict['if'])) {
                $condition = $restrict['if'];
            } elseif ($restrict['ifNot']) {
                $condition = $restrict['ifNot'];
                $negate = true;
            }
            $stats   = (isset($retrievedDoc->stats) && is_array($retrievedDoc->stats)) ? $retrievedDoc->stats : [];
            $matches = null;
            if (0 < preg_match_all('/\$([a-z0-9_]+)/i', $condition, $matches)) {
                foreach ($matches[1] as $i => $match) {
                    if (!isset($stats[$match])) {
                        $stats[$match] = null;
                    }
                    $condition = str_replace($matches[0][$i], 'stats.'.$match, $condition);
                }
            }
            $vars = ['doc' => $doc, 'stats' => (object) $stats];
            if ($negate !== $this->getExpressionService()->evaluate('$'.$condition, $vars)) {
                throw $this->createDeniedException(isset($restrict['message']) ? $restrict['message'] : sprintf('%s is restricted', $operation));
            }
        }
    }
    /**
     * @param string $class
     *
     * @return mixed
     */
    protected function getCrudByModelClass($class)
    {
        return $this->getCrudService()->get($this->getMetaDataService()->getModel($class)['id']);
    }
}
