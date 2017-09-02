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

use Itq\Common\Traits;
use Symfony\Component\ExpressionLanguage\ExpressionLanguage;

/**
 * Expression Service.
 *
 * @author itiQiti Dev Team <opensource@itiqiti.com>
 */
class ExpressionService
{
    use Traits\ServiceTrait;
    use Traits\ExpressionLanguageAwareTrait;
    use Traits\Helper\String\ReplaceVarsTrait;
    use Traits\ServiceAware\TemplateServiceAwareTrait;
    /**
     * @param TemplateService    $templateService
     * @param ExpressionLanguage $expressionLanguage
     */
    public function __construct(TemplateService $templateService, ExpressionLanguage $expressionLanguage)
    {
        $this->setTemplateService($templateService);
        $this->setExpressionLanguage($expressionLanguage);
    }
    /**
     * @param mixed $raw
     * @param mixed $vars
     *
     * @return mixed
     */
    public function evaluate($raw, &$vars)
    {
        $that = $this;

        return $this->replaceVarsCallback(
            $raw,
            $vars,
            function (&$data, &$params) use ($that) {
                $matches = null;
                if (0 < preg_match('/^\$(.+)$/', $data, $matches)) {
                    $data = $that->getExpressionLanguage()->evaluate(trim($matches[1]), $params);

                    return;
                }
                $data = $that->getTemplateService()
                    ->render(
                        'ItqBundle::expression.txt.twig',
                        ['_expression' => $data] + $params
                    ) // @todo remove this dependecy to ItqBundle
                ;
            }
        );
    }
}
