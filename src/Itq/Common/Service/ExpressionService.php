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
        if (is_array($raw)) {
            foreach ($raw as $k => $v) {
                unset($raw[$k]);
                $raw[$this->evaluate($k, $vars)] = $this->evaluate($v, $vars);
            }

            return $raw;
        }

        if (is_object($raw) || is_numeric($raw)) {
            return $raw;
        }

        if (is_string($raw)) {
            $matches = null;
            if (0 < preg_match('/^\$(.+)$/', $raw, $matches)) {
                return $this->getExpressionLanguage()->evaluate(trim($matches[1]), $vars);
            }

            return $this->getTemplateService()
                ->render('AppBundle::expression.txt.twig', ['_expression' => $raw] + $vars) // @todo remove this dependecy to AppBundle
            ;
        }

        return $raw;
    }
}
