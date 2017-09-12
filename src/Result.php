<?php
/**
 * @see       https://github.com/zendframework/zend-datavalidator for the canonical source repository
 * @copyright Copyright (c) 2017 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   https://github.com/zendframework/zend-datavalidator/blob/master/LICENSE.md New BSD License
 */

namespace Zend\DataValidator;

final class Result implements ResultInterface
{
    /** @var bool */
    private $isValid;

    /** ValidationFailureMessage[] */
    private $messages;

    /** @var mixed */
    private $value;

    public function __construct(bool $isValid, $value, array $messages = [])
    {
        $this->isValid = $isValid;
        $this->value = $value;

        array_walk($messages, function ($message) {
            if (! $message instanceof ValidationFailureMessage) {
                throw new Exception\InvalidArgumentException(sprintf(
                    'All validation failure messages must be of type %s; received %s',
                    ValidationFailureMessage::class,
                    is_object($message) ? get_class($message) : gettype($message)
                ));
            }
        });
        $this->messages = $messages;
    }

    public function isValid() : bool
    {
        return $this->isValid;
    }

    /**
     * @return ValidationFailureMessage[]
     */
    public function getMessages() : array
    {
        return $this->messages;
    }

    /**
     * @return mixed
     */
    public function getValue()
    {
        return $this->value;
    }
}
