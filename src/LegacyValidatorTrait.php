<?php
/**
 * @see       https://github.com/zendframework/zend-datavalidator for the canonical source repository
 * @copyright Copyright (c) 2017 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   https://github.com/zendframework/zend-datavalidator/blob/master/LICENSE.md New BSD License
 */

namespace Zend\DataValidator;

/**
 * Trait to use for adapting zend-validator validators as zend-datavalidator validators.
 *
 * Compose this trait into your class, and update it to implement
 * ValidatorInterface from this package; the validator can then be used as a
 * zend-datavalidator instance.
 */
trait LegacyValidatorTrait
{
    public function validate($value, $context = null) : ResultInterface
    {
        if ($this->isValid($value, $context)) {
            return Result::createValidResult($value);
        }

        $messageVariables = array_merge(
            $this->abstractOptions['messageVariables'],
            ['value' => $value]
        );

        $messages = [];
        foreach (array_keys($this->abstractOptions['messages']) as $messageKey) {
            $template = $this->abstractOptions['messageTemplates'][$messageKey];
            $messages[] = new ValidationFailureMessage($messageKey, $template, $messageVariables);
        }
        return Result::createInvalidResult($value, $messages);
    }
}
