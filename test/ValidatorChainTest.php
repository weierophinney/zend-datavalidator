<?php
/**
 * @see       https://github.com/zendframework/zend-datavalidator for the canonical source repository
 * @copyright Copyright (c) 2017 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   https://github.com/zendframework/zend-datavalidator/blob/master/LICENSE.md New BSD License
 */

declare(strict_types=1);

namespace ZendTest\DataValidator;

use PHPUnit\Framework\TestCase;
use Zend\DataValidator\ValidatorChain;
use Zend\DataValidator\ValidatorInterface;
use Zend\DataValidator\Result;
use Zend\DataValidator\ResultAggregate;
use Zend\DataValidator\ResultInterface;
use Zend\DataValidator\ValidationFailureMessage;

class ValidatorChainTest extends TestCase
{
    public function setUp()
    {
        $this->chain = new ValidatorChain();
    }

    public function testReturnsSuccessfulResultIfAllValidationsSucceed()
    {
        $first = new class implements ValidatorInterface {
            public function validate($value, $context = null) : ResultInterface
            {
                return new Result(true, $value);
            }
        };
        $second = clone $first;
        $third = clone $first;

        $this->chain->attach($first);
        $this->chain->attach($second);
        $this->chain->attach($third);

        $result = $this->chain->validate('value', ['context' => 'okay']);
        $this->assertInstanceOf(ResultAggregate::class, $result);
        $this->assertTrue($result->isValid());
        $this->assertSame('value', $result->getValue());
    }

    public function testReturnsInvalidResultIfAnyValidationFails()
    {
        $first = new class implements ValidatorInterface {
            public function validate($value, $context = null) : ResultInterface
            {
                return new Result(true, $value);
            }
        };
        $second = new class implements ValidatorInterface {
            public function validate($value, $context = null) : ResultInterface
            {
                return new Result(false, $value, [
                    new ValidationFailureMessage('invalid', '%value% is invalid', ['value' => $value]),
                ]);
            }
        };
        $third = clone $first;

        $this->chain->attach($first);
        $this->chain->attach($second);
        $this->chain->attach($third);

        $result = $this->chain->validate('value', ['context' => 'okay']);
        $this->assertInstanceOf(ResultAggregate::class, $result);
        $this->assertFalse($result->isValid());
        $this->assertSame('value', $result->getValue());
        $messages = $result->getMessages();
        $this->assertCount(1, $messages);
        $this->assertContainsOnlyInstancesOf(ValidationFailureMessage::class, $messages);
        $message = array_pop($messages);
        $this->assertEquals('invalid', $message->getCode());
        $this->assertEquals('%value% is invalid', $message->getTemplate());
        $this->assertEquals(['value' => 'value'], $message->getVariables());
    }

    public function testReturnsInvalidResultIfAllValidationFails()
    {
        $first = new class implements ValidatorInterface {
            public function validate($value, $context = null) : ResultInterface
            {
                return new Result(false, $value, [
                    new ValidationFailureMessage('invalid', '%value% is invalid', ['value' => $value]),
                ]);
            }
        };
        $second = clone $first;
        $third = clone $first;

        $this->chain->attach($first);
        $this->chain->attach($second);
        $this->chain->attach($third);

        $result = $this->chain->validate('value', ['context' => 'okay']);
        $this->assertInstanceOf(ResultAggregate::class, $result);
        $this->assertFalse($result->isValid());
        $this->assertSame('value', $result->getValue());
        $messages = $result->getMessages();
        $this->assertCount(3, $messages);
        $this->assertContainsOnlyInstancesOf(ValidationFailureMessage::class, $messages);
        foreach ($messages as $message) {
            $this->assertEquals('invalid', $message->getCode());
            $this->assertEquals('%value% is invalid', $message->getTemplate());
            $this->assertEquals(['value' => 'value'], $message->getVariables());
        }
    }

    public function testReturnsTruncatedResultSetWhenOneBreaksOnFailure()
    {
        $first = new class implements ValidatorInterface {
            public function validate($value, $context = null) : ResultInterface
            {
                return new Result(false, $value, [
                    new ValidationFailureMessage('invalid', '%value% is invalid', ['value' => $value]),
                ]);
            }
        };
        $second = clone $first;
        $third = clone $first;

        $this->chain->attach($first);
        $this->chain->attach($second, $breakOnFailure = true);
        $this->chain->attach($third);

        $result = $this->chain->validate('value', ['context' => 'okay']);
        $this->assertInstanceOf(ResultAggregate::class, $result);
        $this->assertFalse($result->isValid());
        $this->assertSame('value', $result->getValue());
        $messages = $result->getMessages();

        // Should only get 2, as second breaks on failure
        $this->assertCount(2, $messages);
        $this->assertContainsOnlyInstancesOf(ValidationFailureMessage::class, $messages);
        foreach ($messages as $message) {
            $this->assertEquals('invalid', $message->getCode());
            $this->assertEquals('%value% is invalid', $message->getTemplate());
            $this->assertEquals(['value' => 'value'], $message->getVariables());
        }
    }
}
