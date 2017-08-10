<?php

/*
 * This file is part of the COMMON package.
 *
 * (c) itiQiti SAS <opensource@itiqiti.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Itq\Common\Validator\Constraints;

use Itq\Common\Traits;
use Itq\Common\Service;
use Itq\Common\ValidationContext;
use Itq\Common\ErrorManagerInterface;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

/**
 * @author itiQiti Dev Team <opensource@itiqiti.com>
 *
 * @Annotation
 */
class ValidationBusinessRulesValidator extends ConstraintValidator
{
    use Traits\ServiceTrait;
    use Traits\ServiceAware\MetaDataServiceAwareTrait;
    use Traits\ServiceAware\BusinessRuleServiceAwareTrait;
    /**
     * @param Service\BusinessRuleService $businessRuleService
     * @param Service\MetaDataService     $metaDataService
     * @param ErrorManagerInterface       $errorManager
     */
    public function __construct(
        Service\BusinessRuleService $businessRuleService,
        Service\MetaDataService $metaDataService,
        ErrorManagerInterface $errorManager
    ) {
        $this->setBusinessRuleService($businessRuleService);
        $this->setMetaDataService($metaDataService);
        $this->setErrorManager($errorManager);
    }
    /**
     * @param mixed      $doc
     * @param Constraint $constraint
     */
    public function validate($doc, Constraint $constraint)
    {
        $events = ['validation', sprintf('validation_group_%s', $this->context->getGroup())];

        if (property_exists($doc, 'type') && $doc->type && $this->context->getGroup()) {
            $events[] = sprintf('validation_%s_type_%s', $this->context->getGroup(), $doc->type);
            $events[] = sprintf('validation_type_%s', $doc->type);
        }

        $context = new ValidationContext($this->getErrorManager());

        foreach ($events as $event) {
            $this->getBusinessRuleService()->executeBusinessRulesForModelOperationWithExecutionContext(
                $context,
                $this->getMetaDataService()->getModelIdForClass($doc),
                $event,
                $doc
            );
        }

        /** @var $symfonyContext ExecutionContextInterface */
        $symfonyContext = $this->context;

        foreach ($context->getFieldsErrors() as $field => $errors) {
            foreach ($errors as $error) {
                /** @var \Exception $exception */
                $exception = $error['exception'];
                $symfonyContext->buildViolation($exception->getMessage())->atPath($field)->addViolation();
            }
        }
    }
}
