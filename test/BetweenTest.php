<?php
/**
 * @see       https://github.com/zendframework/zend-datavalidator for the canonical source repository
 * @copyright Copyright (c) 2017 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   https://github.com/zendframework/zend-datavalidator/blob/master/LICENSE.md New BSD License
 */

namespace ZendTest\DataValidator;

use PHPUnit\Framework\TestCase;
use stdClass;
use Zend\DataValidator\Between;
use Zend\DataValidator\Exception\InvalidArgumentException;
use Zend\DataValidator\Result;

class BetweenTest extends TestCase
{
    public function validationProvider()
    {
        return [
            'inclusive-int-lower-valid'     => [1, 100, true, 1, true],
            'inclusive-int-between-valid'   => [1, 100, true, 10, true],
            'inclusive-int-upper-valid'     => [1, 100, true, 100, true],
            'inclusive-int-lower-invalid'   => [1, 100, true, 0, false],
            'inclusive-int-upper-invalid'   => [1, 100, true, 101, false],
            'inclusive-float-lower-valid'   => [0.01, 0.99, true, 0.02, true],
            'inclusive-float-between-valid' => [0.01, 0.99, true, 0.51, true],
            'inclusive-float-upper-valid'   => [0.01, 0.99, true, 0.98, true],
            'inclusive-float-lower-invalid' => [0.01, 0.99, true, 0.009, false],
            'inclusive-float-upper-invalid' => [0.01, 0.99, true, 1.0, false],
            'exclusive-int-lower-valid'     => [1, 100, false, 2, true],
            'exclusive-int-between-valid'   => [1, 100, false, 10, true],
            'exclusive-int-upper-valid'     => [1, 100, false, 99, true],
            'exclusive-int-lower-invalid'   => [1, 100, false, 1, false],
            'exclusive-int-upper-invalid'   => [1, 100, false, 100, false],
            'exclusive-float-lower-valid'   => [0.01, 0.99, false, 0.02, true],
            'exclusive-float-between-valid' => [0.01, 0.99, false, 0.51, true],
            'exclusive-float-upper-valid'   => [0.01, 0.99, false, 0.98, true],
            'exclusive-float-lower-invalid' => [0.01, 0.99, false, 0.01, false],
            'exclusive-float-upper-invalid' => [0.01, 0.99, false, 0.99, false],
        ];
    }

    /**
     * @dataProvider validationProvider
     */
    public function testValidateReturnsExpectedResults(
        $min,
        $max,
        bool $inclusive,
        $input,
        bool $expectedResult
    ) {
        $validator = new Between($min, $max, $inclusive);
        $result = $validator->validate($input);
        $this->assertInstanceOf(Result::class, $result);
        $this->assertSame(
            $expectedResult,
            $result->isValid(),
            'Failed value: ' . $input
        );
    }

    public function invalidConstructorValues()
    {
        return [
            'invalid-min-null'   => [null, 1, '"min"'],
            'invalid-min-false'  => [false, 1, '"min"'],
            'invalid-min-true'   => [true, 1, '"min"'],
            'invalid-min-string' => ['invalid', 1, '"min"'],
            'invalid-min-array'  => [[], 1, '"min"'],
            'invalid-min-object' => [new stdClass(), 1, '"min"'],
            'invalid-max-null'   => [1, null, '"max"'],
            'invalid-max-false'  => [1, false, '"max"'],
            'invalid-max-true'   => [1, true, '"max"'],
            'invalid-max-string' => [1, 'invalid', '"max"'],
            'invalid-max-array'  => [1, [], '"max"'],
            'invalid-max-object' => [1, new stdClass(), '"max"'],
        ];
    }

    /**
     * @dataProvider invalidConstructorValues
     */
    public function testRaisesExceptionForInvalidMinAndMaxValues(
        $min,
        $max,
        string $expectedExceptionMessage
    ) {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage($expectedExceptionMessage);
        new Between($min, $max);
    }

    /**
     * Ensures that getMin() returns expected value
     *
     * @return void
     */
    public function testGetMin()
    {
        $validator = new Between(1, 10);
        $this->assertEquals(1, $validator->getMin());
    }

    /**
     * Ensures that getMax() returns expected value
     *
     * @return void
     */
    public function testGetMax()
    {
        $validator = new Between(1, 10);
        $this->assertEquals(10, $validator->getMax());
    }

    /**
     * Ensures that isInclusive() returns expected default value
     *
     * @return void
     */
    public function testDefaultInclusiveFlagIsTrue()
    {
        $validator = new Between(1, 10);
        $this->assertTrue($validator->isInclusive());
    }

    public function testCanPassInclusiveFlagToConstructor()
    {
        $validator = new Between(1, 10, false);
        $this->assertFalse($validator->isInclusive());
    }

    public function testEqualsMessageVariables()
    {
        $validator = new Between(1, 10);
        $this->assertAttributeEquals(['min' => 1, 'max' => 10], 'messageVariables', $validator);
    }
}
