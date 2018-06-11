<?php
/**
 * @see       https://github.com/zendframework/zend-datavalidator for the canonical source repository
 * @copyright Copyright (c) 2017 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   https://github.com/zendframework/zend-datavalidator/blob/master/LICENSE.md New BSD License
 */

namespace Zend\DataValidator;

abstract class AbstractValidator implements ValidatorInterface
{
    /**
     * Array of validation failure message templates. Should be an array of
     * key value pairs, to allow both lookup of templates by key, as well as
     * overriding the message template string.
     *
     * Keys are used as validation failure message codes, and should be unique.
     *
     * @var string[]
     */
    protected $messageTemplates = [];

    /**
     * Array of variable subsitutions to make in message templates. Typically,
     * these will be validator constraint values. The message templates will
     * refer to them as `%name%`.
     *
     * @var array
     */
    protected $messageVariables = [];

    /**
     * Create and return a result indicating validation failure.
     *
     * Use this within validators to create the validation result when a failure
     * condition occurs. Pass it the value, and an array of message keys.
     */
    protected function createInvalidResult($value, array $messageKeys) : Result
    {
        $messages = array_map(function ($key) {
            return new ValidationFailureMessage(
                $key,
                $this->getMessageTemplate($key),
                $this->messageVariables
            );
        }, $messageKeys);

        return Result::createInvalidResult($value, $messages);
    }

    /**
     * Returns the message templates from the validator
     *
     * @return string[]
     */
    public function getMessageTemplates() : array
    {
        return $this->messageTemplates;
    }

    /**
     * Sets the validation failure message template for a particular key
     */
    public function setMessageTemplate(string $messageKey, string $messageString) : void
    {
        $this->messageTemplates[$messageKey] = $messageString;
    }

    /**
     * Finds and returns the message template associated with the given message key.
     */
    protected function getMessageTemplate(string $messageKey) : string
    {
        return $this->messageTemplates[$messageKey] ?? '';
    }
}
