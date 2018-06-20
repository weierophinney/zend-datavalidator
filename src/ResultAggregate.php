<?php
/**
 * @see       https://github.com/zendframework/zend-datavalidator for the canonical source repository
 * @copyright Copyright (c) 2017-2018 Zend Technologies USA Inc. (https://www.zend.com)
 * @license   https://github.com/zendframework/zend-datavalidator/blob/master/LICENSE.md New BSD License
 */

declare(strict_types=1);

namespace Zend\DataValidator;

/**
 * Aggregate of `ResultInterface` instances, generally produced by a validation chain.
 */
final class ResultAggregate implements ResultInterface
{
    /** @var ResultInterface[] */
    private $results = [];

    /** @var mixed */
    private $value;

    /**
     * @param mixed $value
     */
    public function __construct($value)
    {
        $this->value = $value;
    }

    public function push(ResultInterface $result) : void
    {
        $this->results[] = $result;
    }

    public function isValid() : bool
    {
        $this->assertAggregateNotEmpty();

        return array_reduce($this->results, function ($isValid, $result) {
            return $isValid === false ? false : $result->isValid();
        }, null);
    }

    /**
     * @return ValidationFailureMessage[]
     */
    public function getMessages() : array
    {
        $this->assertAggregateNotEmpty();

        return array_reduce($this->results, function ($messages, $result) {
            return $result->isValid()
                ? $messages
                : array_merge($messages, $result->getMessages());
        }, []);
    }

    /**
     * @return mixed
     */
    public function getValue()
    {
        return $this->value;
    }

    private function assertAggregateNotEmpty() : void
    {
        if (empty($this->results)) {
            throw MissingResultsException::forClass(self::class);
        }
    }
}
