<?php
/**
 * @see       https://github.com/zendframework/zend-datavalidator for the canonical source repository
 * @copyright Copyright (c) 2018 Zend Technologies USA Inc. (https://www.zend.com)
 * @license   https://github.com/zendframework/zend-datavalidator/blob/master/LICENSE.md New BSD License
 */

declare(strict_types=1);

namespace ZendTest\DataValidator;

use PHPUnit\Framework\TestCase;
use stdClass;
use Zend\DataValidator\Exception\InvalidArgumentException;
use Zend\DataValidator\NotEmpty;

class NotEmptyTest extends TestCase
{
    public function combineTypesAndAssertionsForProvider(array $types, array $assertions) : iterable
    {
        foreach ($types as $argType => $type) {
            foreach ($assertions as $assertion => $data) {
                $name = sprintf('%s-%s', $argType, $assertion);
                array_unshift($data, $type);
                yield $name => $data;
            }
        }
    }

    public function constructorWithTypeArrayProvider() : array
    {
        return [
            'php-boolean'               => [['php', 'boolean'], NotEmpty::PHP],
            'boolean-boolean'           => [['boolean', 'boolean'], NotEmpty::BOOLEAN],
            'php-boolean-constants'     => [[NotEmpty::PHP, NotEmpty::BOOLEAN], NotEmpty::PHP],
            'boolean-boolean-constants' => [[NotEmpty::BOOLEAN, NotEmpty::BOOLEAN], NotEmpty::BOOLEAN],
        ];
    }

    /**
     * Ensures that the constructor allows an array of types, and that none are
     * duplicated.
     *
     * @dataProvider constructorWithTypeArrayProvider
     */
    public function testConstructorWithTypeMaskArray(array $types, int $expected)
    {
        $validator = new NotEmpty($types);
        $this->assertAttributeSame($expected, 'typeMask', $validator);
    }

    /**
     * Provides values and expected validity for the basic test
     */
    public function basicProvider() : array
    {
        return [
            'string-not-empty'        => ['word', true],
            'string-empty'            => ['', false],
            'string-whitespace'       => ['    ', false],
            'string-whitespace+words' => ['  word  ', true],
            'string-zero'             => ['0', true],
            'string-one'              => [1, true],
            'int-zero'                => [0, true],
            'bool-true'               => [true, true],
            'bool-false'              => [false, false],
            'null'                    => [null, false],
            'array-empty'             => [[], false],
            'array-not-empty'         => [[5], true],
            'float-zero'              => [0.0, true],
            'float-one'               => [1.0, true],
            'object'                  => [new stdClass(), true],
            'object-string-empty'     => [new TestAsset\StringSerializable(), true],
            'object-string'           => [new TestAsset\StringSerializable('xxx'), true],
            'object-count-empty'      => [new TestAsset\IntegerSerializable(), true],
            'object-count'            => [new TestAsset\IntegerSerializable(1), true],
        ];
    }

    /**
     * Ensures that the validator follows expected behavior with a default type mask.
     *
     * @dataProvider basicProvider
     * @param mixed $value Value to test
     */
    public function testBasic($value, bool $expectedValidity)
    {
        $validator = new NotEmpty();
        $result = $validator->validate($value);
        $this->assertSame($expectedValidity, $result->isValid());
    }

    /**
     * Provides values and their expected validity by typemask
     */
    public function assertionsByTypeMaskProvider() : iterable
    {
        yield from $this->combineTypesAndAssertionsForProvider(
            [
                'boolean-constant' => NotEmpty::BOOLEAN,
                'boolean-string'   => 'boolean',
            ],
            [
                'false'               => [false, false],
                'true'                => [true, true],
                'int-zero'            => [0, true],
                'int-one'             => [1, true],
                'float-zero'          => [0.0, true],
                'float-one'           => [1.0, true],
                'string-empty'        => ['', true],
                'string-not-empty'    => ['abc', true],
                'string-zero'         => ['0', true],
                'string-one'          => ['1', true],
                'array-empty'         => [[], true],
                'array-not-empty'     => [['xxx'], true],
                'null'                => [null, true],
                'object'              => [new stdClass(), false],
                'object-string-empty' => [new TestAsset\StringSerializable(), false],
                'object-string'       => [new TestAsset\StringSerializable('xxx'), false],
                'object-count-empty'  => [new TestAsset\IntegerSerializable(), false],
                'object-count'        => [new TestAsset\IntegerSerializable(1), false],
            ]
        );

        yield from $this->combineTypesAndAssertionsForProvider(
            [
                'integer-constant' => NotEmpty::INTEGER,
                'integer-string'   => 'integer',
            ],
            [
                'false'               => [false, true],
                'true'                => [true, true],
                'int-zero'            => [0, false],
                'int-one'             => [1, true],
                'float-zero'          => [0.0, true],
                'float-one'           => [1.0, true],
                'string-empty'        => ['', true],
                'string-not-empty'    => ['abc', true],
                'string-zero'         => ['0', true],
                'string-one'          => ['1', true],
                'array-empty'         => [[], true],
                'array-not-empty'     => [['xxx'], true],
                'null'                => [null, true],
                'object'              => [new stdClass(), false],
                'object-string-empty' => [new TestAsset\StringSerializable(), false],
                'object-string'       => [new TestAsset\StringSerializable('xxx'), false],
                'object-count-empty'  => [new TestAsset\IntegerSerializable(), false],
                'object-count'        => [new TestAsset\IntegerSerializable(1), false],
            ]
        );

        yield from $this->combineTypesAndAssertionsForProvider(
            [
                'float-constant' => NotEmpty::FLOAT,
                'float-string'   => 'float',
            ],
            [
                'false'               => [false, true],
                'true'                => [true, true],
                'int-zero'            => [0, true],
                'int-one'             => [1, true],
                'float-zero'          => [0.0, false],
                'float-one'           => [1.0, true],
                'string-empty'        => ['', true],
                'string-not-empty'    => ['abc', true],
                'string-zero'         => ['0', true],
                'string-one'          => ['1', true],
                'array-empty'         => [[], true],
                'array-not-empty'     => [['xxx'], true],
                'null'                => [null, true],
                'object'              => [new stdClass(), false],
                'object-string-empty' => [new TestAsset\StringSerializable(), false],
                'object-string'       => [new TestAsset\StringSerializable('xxx'), false],
                'object-count-empty'  => [new TestAsset\IntegerSerializable(), false],
                'object-count'        => [new TestAsset\IntegerSerializable(1), false],
            ]
        );

        yield from $this->combineTypesAndAssertionsForProvider(
            [
                'string-constant' => NotEmpty::STRING,
                'string-string'   => 'string',
            ],
            [
                'false'               => [false, true],
                'true'                => [true, true],
                'int-zero'            => [0, true],
                'int-one'             => [1, true],
                'float-zero'          => [0.0, true],
                'float-one'           => [1.0, true],
                'string-empty'        => ['', false],
                'string-not-empty'    => ['abc', true],
                'string-zero'         => ['0', true],
                'string-one'          => ['1', true],
                'array-empty'         => [[], true],
                'array-not-empty'     => [['xxx'], true],
                'null'                => [null, true],
                'object'              => [new stdClass(), false],
                'object-string-empty' => [new TestAsset\StringSerializable(), false],
                'object-string'       => [new TestAsset\StringSerializable('xxx'), false],
                'object-count-empty'  => [new TestAsset\IntegerSerializable(), false],
                'object-count'        => [new TestAsset\IntegerSerializable(1), false],
            ]
        );

        yield from $this->combineTypesAndAssertionsForProvider(
            [
                'zero-constant' => NotEmpty::ZERO,
                'zero-string'   => 'zero',
            ],
            [
                'false'               => [false, true],
                'true'                => [true, true],
                'int-zero'            => [0, true],
                'int-one'             => [1, true],
                'float-zero'          => [0.0, true],
                'float-one'           => [1.0, true],
                'string-empty'        => ['', true],
                'string-not-empty'    => ['abc', true],
                'string-zero'         => ['0', false],
                'string-one'          => ['1', true],
                'array-empty'         => [[], true],
                'array-not-empty'     => [['xxx'], true],
                'null'                => [null, true],
                'object'              => [new stdClass(), false],
                'object-string-empty' => [new TestAsset\StringSerializable(), false],
                'object-string'       => [new TestAsset\StringSerializable('xxx'), false],
                'object-count-empty'  => [new TestAsset\IntegerSerializable(), false],
                'object-count'        => [new TestAsset\IntegerSerializable(1), false],
            ]
        );

        yield from $this->combineTypesAndAssertionsForProvider(
            [
                'array-constant' => NotEmpty::EMPTY_ARRAY,
                'array-string'   => 'array',
            ],
            [
                'false'               => [false, true],
                'true'                => [true, true],
                'int-zero'            => [0, true],
                'int-one'             => [1, true],
                'float-zero'          => [0.0, true],
                'float-one'           => [1.0, true],
                'string-empty'        => ['', true],
                'string-not-empty'    => ['abc', true],
                'string-zero'         => ['0', true],
                'string-one'          => ['1', true],
                'array-empty'         => [[], false],
                'array-not-empty'     => [['xxx'], true],
                'null'                => [null, true],
                'object'              => [new stdClass(), false],
                'object-string-empty' => [new TestAsset\StringSerializable(), false],
                'object-string'       => [new TestAsset\StringSerializable('xxx'), false],
                'object-count-empty'  => [new TestAsset\IntegerSerializable(), false],
                'object-count'        => [new TestAsset\IntegerSerializable(1), false],
            ]
        );

        yield from $this->combineTypesAndAssertionsForProvider(
            [
                'null-constant' => NotEmpty::NULL,
                'null-string'   => 'null',
            ],
            [
                'false'               => [false, true],
                'true'                => [true, true],
                'int-zero'            => [0, true],
                'int-one'             => [1, true],
                'float-zero'          => [0.0, true],
                'float-one'           => [1.0, true],
                'string-empty'        => ['', true],
                'string-not-empty'    => ['abc', true],
                'string-zero'         => ['0', true],
                'string-one'          => ['1', true],
                'array-empty'         => [[], true],
                'array-not-empty'     => [['xxx'], true],
                'null'                => [null, false],
                'object'              => [new stdClass(), false],
                'object-string-empty' => [new TestAsset\StringSerializable(), false],
                'object-string'       => [new TestAsset\StringSerializable('xxx'), false],
                'object-count-empty'  => [new TestAsset\IntegerSerializable(), false],
                'object-count'        => [new TestAsset\IntegerSerializable(1), false],
            ]
        );

        yield from $this->combineTypesAndAssertionsForProvider(
            [
                'php-constant' => NotEmpty::PHP,
                'php-string'   => 'php',
            ],
            [
                'false'               => [false, false],
                'true'                => [true, true],
                'int-zero'            => [0, false],
                'int-one'             => [1, true],
                'float-zero'          => [0.0, false],
                'float-one'           => [1.0, true],
                'string-empty'        => ['', false],
                'string-not-empty'    => ['abc', true],
                'string-zero'         => ['0', false],
                'string-one'          => ['1', true],
                'array-empty'         => [[], false],
                'array-not-empty'     => [['xxx'], true],
                'null'                => [null, false],
                'object'              => [new stdClass(), false],
                'object-string-empty' => [new TestAsset\StringSerializable(), false],
                'object-string'       => [new TestAsset\StringSerializable('xxx'), false],
                'object-count-empty'  => [new TestAsset\IntegerSerializable(), false],
                'object-count'        => [new TestAsset\IntegerSerializable(1), false],
            ]
        );

        yield from $this->combineTypesAndAssertionsForProvider(
            [
                'space-constant' => NotEmpty::SPACE,
                'space-string'   => 'space',
            ],
            [
                'false'               => [false, true],
                'true'                => [true, true],
                'int-zero'            => [0, true],
                'int-one'             => [1, true],
                'float-zero'          => [0.0, true],
                'float-one'           => [1.0, true],
                'string-empty'        => ['', true],
                'string-not-empty'    => ['abc', true],
                'string-zero'         => ['0', true],
                'string-one'          => ['1', true],
                'array-empty'         => [[], true],
                'array-not-empty'     => [['xxx'], true],
                'null'                => [null, true],
                'object'              => [new stdClass(), false],
                'object-string-empty' => [new TestAsset\StringSerializable(), false],
                'object-string'       => [new TestAsset\StringSerializable('xxx'), false],
                'object-count-empty'  => [new TestAsset\IntegerSerializable(), false],
                'object-count'        => [new TestAsset\IntegerSerializable(1), false],
            ]
        );

        yield from $this->combineTypesAndAssertionsForProvider(
            [
                'all-constant' => NotEmpty::ALL,
                'all-string'   => 'all',
            ],
            [
                'false'               => [false, false],
                'true'                => [true, true],
                'int-zero'            => [0, false],
                'int-one'             => [1, true],
                'float-zero'          => [0.0, false],
                'float-one'           => [1.0, true],
                'string-empty'        => ['', false],
                'string-not-empty'    => ['abc', true],
                'string-zero'         => ['0', false],
                'string-one'          => ['1', true],
                'array-empty'         => [[], false],
                'array-not-empty'     => [['xxx'], true],
                'null'                => [null, false],
                // All object validations fail unless the object is both string
                // serializable and countable
                'object'              => [new stdClass(), false],
                'object-string-empty' => [new TestAsset\StringSerializable(), false],
                'object-string'       => [new TestAsset\StringSerializable('xxx'), false],
                'object-count-empty'  => [new TestAsset\IntegerSerializable(), false],
                'object-count'        => [new TestAsset\IntegerSerializable(1), false],
                'object-serializable-all-empty' => [new TestAsset\StringAndIntegerSerializable(), false],
                'object-serializable-int-empty' => [new TestAsset\StringAndIntegerSerializable('xxx'), false],
                'object-serializable-string-empty' => [new TestAsset\StringAndIntegerSerializable('', 1), false],
                'object-serializable' => [new TestAsset\StringAndIntegerSerializable('xxx', 1), true],
            ]
        );

        yield from $this->combineTypesAndAssertionsForProvider(
            [
                'object-constant' => NotEmpty::OBJECT,
                'object-string'   => 'object',
            ],
            [
                'false'               => [false, true],
                'true'                => [true, true],
                'int-zero'            => [0, true],
                'int-one'             => [1, true],
                'float-zero'          => [0.0, true],
                'float-one'           => [1.0, true],
                'string-empty'        => ['', true],
                'string-not-empty'    => ['abc', true],
                'string-zero'         => ['0', true],
                'string-one'          => ['1', true],
                'array-empty'         => [[], true],
                'array-not-empty'     => [['xxx'], true],
                'null'                => [null, true],
                'object'              => [new stdClass(), true],
                'object-string-empty' => [new TestAsset\StringSerializable(), true],
                'object-string'       => [new TestAsset\StringSerializable('xxx'), true],
                'object-count-empty'  => [new TestAsset\IntegerSerializable(), true],
                'object-count'        => [new TestAsset\IntegerSerializable(1), true],
            ]
        );

        yield from $this->combineTypesAndAssertionsForProvider(
            [
                'objectstring-constant' => NotEmpty::OBJECT_STRING,
                'objectstring-string'   => 'objectstring',
            ],
            [
                'false'               => [false, true],
                'true'                => [true, true],
                'int-zero'            => [0, true],
                'int-one'             => [1, true],
                'float-zero'          => [0.0, true],
                'float-one'           => [1.0, true],
                'string-empty'        => ['', true],
                'string-not-empty'    => ['abc', true],
                'string-zero'         => ['0', true],
                'string-one'          => ['1', true],
                'array-empty'         => [[], true],
                'array-not-empty'     => [['xxx'], true],
                'null'                => [null, true],
                'object'              => [new stdClass(), false],
                'object-string-empty' => [new TestAsset\StringSerializable(), false],
                'object-string'       => [new TestAsset\StringSerializable('xxx'), true],
                'object-count-empty'  => [new TestAsset\IntegerSerializable(), false],
                'object-count'        => [new TestAsset\IntegerSerializable(1), false],
            ]
        );

        yield from $this->combineTypesAndAssertionsForProvider(
            [
                'objectcount-constant' => NotEmpty::OBJECT_COUNT,
                'objectcount-string'   => 'objectcount',
            ],
            [
                'false'               => [false, true],
                'true'                => [true, true],
                'int-zero'            => [0, true],
                'int-one'             => [1, true],
                'float-zero'          => [0.0, true],
                'float-one'           => [1.0, true],
                'string-empty'        => ['', true],
                'string-not-empty'    => ['abc', true],
                'string-zero'         => ['0', true],
                'string-one'          => ['1', true],
                'array-empty'         => [[], true],
                'array-not-empty'     => [['xxx'], true],
                'null'                => [null, true],
                'object'              => [new stdClass(), false],
                'object-string-empty' => [new TestAsset\StringSerializable(), false],
                'object-string'       => [new TestAsset\StringSerializable('xxx'), false],
                'object-count-empty'  => [new TestAsset\IntegerSerializable(), false],
                'object-count'        => [new TestAsset\IntegerSerializable(1), true],
            ]
        );

        yield from $this->combineTypesAndAssertionsForProvider(
            [
                'multi-constants'          => [NotEmpty::ZERO, NotEmpty::STRING, NotEmpty::BOOLEAN],
                'multi-constants-addition' => NotEmpty::ZERO + NotEmpty::STRING + NotEmpty::BOOLEAN,
                'multi-constants-OR'       => NotEmpty::ZERO | NotEmpty::STRING | NotEmpty::BOOLEAN,
                'multi-strings'            => ['zero', 'string', 'boolean'],
            ],
            [
                'false'               => [false, false],
                'true'                => [true, true],
                'int-zero'            => [0, true],
                'int-one'             => [1, true],
                'float-zero'          => [0.0, true],
                'float-one'           => [1.0, true],
                'string-empty'        => ['', false],
                'string-not-empty'    => ['abc', true],
                'string-zero'         => ['0', false],
                'string-one'          => ['1', true],
                'array-empty'         => [[], true],
                'array-not-empty'     => [['xxx'], true],
                'null'                => [null, true],
                'object'              => [new stdClass(), false],
                'object-string-empty' => [new TestAsset\StringSerializable(), false],
                'object-string'       => [new TestAsset\StringSerializable('xxx'), false],
                'object-count-empty'  => [new TestAsset\IntegerSerializable(), false],
                'object-count'        => [new TestAsset\IntegerSerializable(1), false],
            ]
        );
    }

    /**
     * Ensures that the validator follows expected behavior when typemask is BOOLEAN
     *
     * @dataProvider assertionsByTypeMaskProvider
     * @param string|int $typeMask
     * @param mixed $value Value to test
     */
    public function testValidityByTypeMask($typeMask, $value, bool $expectedValidity)
    {
        $validator = new NotEmpty($typeMask);
        $result = $validator->validate($value);
        $this->assertSame($expectedValidity, $result->isValid());
    }

    /**
     * Data provider for testStringNotationWithDuplicate method. Provides a string which will be duplicated. The test
     * ensures that setting a string value more than once only turns on the appropriate bit once
     */
    public function duplicateStringSettingProvider() : array
    {
        return [
            'boolean'      => ['boolean',      NotEmpty::BOOLEAN],
            'integer'      => ['integer',      NotEmpty::INTEGER],
            'float'        => ['float',        NotEmpty::FLOAT],
            'string'       => ['string',       NotEmpty::STRING],
            'zero'         => ['zero',         NotEmpty::ZERO],
            'array'        => ['array',        NotEmpty::EMPTY_ARRAY],
            'null'         => ['null',         NotEmpty::NULL],
            'php'          => ['php',          NotEmpty::PHP],
            'space'        => ['space',        NotEmpty::SPACE],
            'object'       => ['object',       NotEmpty::OBJECT],
            'objectstring' => ['objectstring', NotEmpty::OBJECT_STRING],
            'objectcount'  => ['objectcount',  NotEmpty::OBJECT_COUNT],
            'all'          => ['all',          NotEmpty::ALL],
        ];
    }

    /**
     * Ensures that the validator follows expected behavior so if a string is specified more than once, it doesn't
     * cause different validations to run
     *
     * @dataProvider duplicateStringSettingProvider
     */
    public function testStringNotationWithDuplicate(string $string, int $expected)
    {
        $type = [$string, $string];
        $validator = new NotEmpty([$string, $string]);

        $this->assertAttributeSame($expected, 'typeMask', $validator);
    }
}
