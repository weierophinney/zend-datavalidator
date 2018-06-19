<?php
/**
 * @see       https://github.com/zendframework/zend-datavalidator for the canonical source repository
 * @copyright Copyright (c) 2017 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   https://github.com/zendframework/zend-datavalidator/blob/master/LICENSE.md New BSD License
 */

declare(strict_types=1);

namespace ZendTest\DataValidator;

use PHPUnit\Framework\TestCase;
use Zend\DataValidator\LegacyValidatorTrait;
use Zend\DataValidator\ValidationFailureMessage;
use Zend\DataValidator\ValidatorInterface;
use Zend\DataValidator\Result;
use Zend\Validator\GreaterThan as GreaterThanValidator;

class LegacyValidatorTraitTest extends TestCase
{
    public function setUp()
    {
        $args = [
            'min' => 5,
        ];
        $this->validator = new class($args) extends GreaterThanValidator implements ValidatorInterface {
            use LegacyValidatorTrait;
        };
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
