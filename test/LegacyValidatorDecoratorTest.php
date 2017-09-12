<?php
/**
 * @see       https://github.com/zendframework/zend-datavalidator for the canonical source repository
 * @copyright Copyright (c) 2017 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   https://github.com/zendframework/zend-datavalidator/blob/master/LICENSE.md New BSD License
 */

namespace ZendTest\DataValidator;

use PHPUnit\Framework\TestCase;
use Zend\DataValidator\LegacyValidatorDecorator;
use Zend\DataValidator\ValidationFailureMessage;
use Zend\DataValidator\ValidatorInterface;
use Zend\DataValidator\Result;
use Zend\Validator\GreaterThan as GreaterThanValidator;

class LegacyValidatorDecoratorTest extends TestCase
{
    public function setUp()
    {
        $this->validator = new LegacyValidatorDecorator(
            new GreaterThanValidator(['min' => 5])
        );
    }

    public function testValidValueResultsInValidResultInstance()
    {
        $result = $this->validator->validate(123);
        $this->assertInstanceOf(Result::class, $result);
        $this->assertTrue($result->isValid());
        $this->assertSame(123, $result->getValue());
    }

    public function testInvalidValueResultsInInvalidResultInstanceWithMessages()
    {
        $result = $this->validator->validate(1);
        $this->assertInstanceOf(Result::class, $result);
        $this->assertFalse($result->isValid());
        $this->assertSame(1, $result->getValue());
        $messages = $result->getMessages();
        $this->assertInternalType('array', $messages);
        $this->assertCount(1, $messages);
        $this->assertContainsOnlyInstancesOf(ValidationFailureMessage::class, $messages);
        $message = array_pop($messages);
        $this->assertSame(GreaterThanValidator::NOT_GREATER, $message->getCode());
    }
}
