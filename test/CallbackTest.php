<?php
/**
 * @see       https://github.com/zendframework/zend-datavalidator for the canonical source repository
 * @copyright Copyright (c) 2017 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   https://github.com/zendframework/zend-datavalidator/blob/master/LICENSE.md New BSD License
 */

declare(strict_types=1);

namespace ZendTest\DataValidator;

use PHPUnit\Framework\TestCase;
use Zend\DataValidator\Callback;
use Zend\DataValidator\Exception\InvalidCallbackResultException;
use Zend\DataValidator\Result;
use Zend\DataValidator\ResultInterface;
use Zend\DataValidator\ValidationFailureMessage;

class CallbackTest extends TestCase
{
    public function testValidationReturnsResultValuesFromCallbacksVerbatim()
    {
        $expected = new Result(true, 'value');
        $callback = function ($value) use ($expected) {
            return $expected;
        };
        $validator = new Callback($callback);
        $result = $validator->validate('value');
        $this->assertSame($expected, $result);
    }

    public function invalidReturnValues()
    {
        return [
            'null'       => [null],
            'zero'       => [0],
            'int'        => [1],
            'zero-float' => [0.0],
            'float'      => [1.1],
            'string'     => ['string'],
            'array'      => [['string']],
            'object'     => [(object) ['value' => 'string']],
        ];
    }

    /**
     * @dataProvider invalidReturnValues
     */
    public function testValidationRaisesExceptionForNonResultNonBooleanCallbackReturnValues($returnValue)
    {
        $callback = function ($value) use ($returnValue) {
            return $returnValue;
        };
        $validator = new Callback($callback);

        $this->expectException(InvalidCallbackResultException::class);
        $result = $validator->validate('value');
    }

    public function testValidationReturnsValidResultForBooleanTrueCallbackReturnValue()
    {
        $callback = function ($value) {
            return true;
        };
        $validator = new Callback($callback);
        $result = $validator->validate('value');

        $this->assertInstanceOf(ResultInterface::class, $result);
        $this->assertTrue($result->isValid());
        $this->assertSame('value', $result->getValue());
    }

    public function testValidationReturnsInvalidResultForBooleanFalseCallbackReturnValue()
    {
        $callback = function ($value) {
            return false;
        };
        $validator = new Callback($callback);
        $result = $validator->validate('value');

        $this->assertInstanceOf(ResultInterface::class, $result);
        $this->assertFalse($result->isValid());
        $this->assertSame('value', $result->getValue());
        $messages = $result->getMessages();
        $this->assertCount(1, $messages);
        $message = array_pop($messages);
        $this->assertInstanceOf(ValidationFailureMessage::class, $message);
        $this->assertSame(Callback::INVALID, $message->getCode());
    }
}
