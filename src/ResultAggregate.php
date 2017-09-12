<?php
/**
 * @see       https://github.com/zendframework/zend-datavalidator for the canonical source repository
 * @copyright Copyright (c) 2017 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   https://github.com/zendframework/zend-datavalidator/blob/master/LICENSE.md New BSD License
 */

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

    public function push(ResultInterface $result)
    {
        $this->results[] = $result;
    }

    public function isValid() : bool
    {
        $this->assertAggregateNotEmpty();

        return array_reduce($this->results, function ($isValid, $result) {
            if ($isValid === false) {
                return false;
            }
            return $result->isValid();
        }, null);
    }

    /**
     * @return ValidationFailureMessage[]
     */
    public function getMessages() : array
    {
        $this->assertAggregateNotEmpty();

        return array_reduce($this->results, function ($messages, $result) {
            if ($result->isValid()) {
                return $messages;
            }
            return array_merge($messages, $result->getMessages());
        }, []);
    }

    /**
     * @return mixed
     */
    public function getValue()
    {
        return $this->value;
    }

    private function assertAggregateNotEmpty()
    {
        if (0 === count($this->results)) {
            throw MissingResultsException::forClass(self::class);
        }
    }
}
