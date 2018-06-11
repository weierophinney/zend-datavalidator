<?php
/**
 * @see       https://github.com/zendframework/zend-datavalidator for the canonical source repository
 * @copyright Copyright (c) 2017 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   https://github.com/zendframework/zend-datavalidator/blob/master/LICENSE.md New BSD License
 */

namespace Zend\DataValidator;

use ReflectionProperty;
use Zend\Validator\AbstractValidator as AbstractLegacyValidator;
use Zend\Validator\ValidatorInterface as LegacyValidatorInterface;

/**
 * Decorates a zend-validator ValidatorInterface instance to work as a
 * zend-datavalidator ValidatorInterface instance.
 *
 * Pass the legacy validator to the constructor. When `validate()` is called,
 * it proxies to the legacy validator, and then creates a `Result` instance
 * based on the results of validation.
 *
 * When the legacy validator implements the zend-validator AbstractValidator,
 * it will pull the message templates and variables from it via reflection
 * in order to create `ValidationFailureMessage` instances. Otherwise, it
 * creates them from the results of `getMessages()`, passing no message
 * variables to the list.
 */
final class LegacyValidatorDecorator implements ValidatorInterface
{
    /** @var LegacyValidatorInterface */
    private $legacyValidator;

    public function __construct(LegacyValidatorInterface $legacyValidator)
    {
        $this->legacyValidator = $legacyValidator;
    }

    public function validate($value, $context = null) : ResultInterface
    {
        if ($this->legacyValidator->isValid($value, $context)) {
            return Result::createValidResult($value);
        }

        if ($this->legacyValidator instanceof AbstractLegacyValidator) {
            return $this->marshalResultFromAbstractValidator($value);
        }

        return $this->marshalResultFromLegacyValidator($value);
    }

    private function marshalResultFromAbstractValidator($value) : ResultInterface
    {
        $r = new ReflectionProperty($this->legacyValidator, 'abstractOptions');
        $r->setAccessible(true);
        $options = $r->getValue($this->legacyValidator);

        $messageVariables = array_merge(
            $options['messageVariables'],
            ['value' => $value]
        );

        $messages = [];
        foreach (array_keys($options['messages']) as $messageKey) {
            $template = $options['messageTemplates'][$messageKey];
            $messages[] = new ValidationFailureMessage($messageKey, $template, $messageVariables);
        }
        return Result::createInvalidResult($value, $messages);
    }

    private function marshalResultFromLegacyValidator($value) : ResultInterface
    {
        $messages = [];
        foreach ($this->legacyValidator->getMessages() as $code => $message) {
            $messages[] = new ValidationFailureMessage($code, $message);
        }
        return Result::createInvalidResult($value, $messages);
    }
}
