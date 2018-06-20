<?php
/**
 * @see       https://github.com/zendframework/zend-datavalidator for the canonical source repository
 * @copyright Copyright (c) 2018 Zend Technologies USA Inc. (https://www.zend.com)
 * @license   https://github.com/zendframework/zend-datavalidator/blob/master/LICENSE.md New BSD License
 */

declare(strict_types=1);

namespace Zend\DataValidator;

use Countable;

use function array_search;
use function count;
use function in_array;
use function is_array;
use function is_bool;
use function is_float;
use function is_int;
use function is_object;
use function is_string;
use function method_exists;
use function preg_match;

final class NotEmpty extends AbstractValidator
{
    public const BOOLEAN       = 0b000000000001;
    public const INTEGER       = 0b000000000010;
    public const FLOAT         = 0b000000000100;
    public const STRING        = 0b000000001000;
    public const ZERO          = 0b000000010000;
    public const EMPTY_ARRAY   = 0b000000100000;
    public const NULL          = 0b000001000000;
    public const PHP           = 0b000001111111;
    public const SPACE         = 0b000010000000;
    public const OBJECT        = 0b000100000000;
    public const OBJECT_STRING = 0b001000000000;
    public const OBJECT_COUNT  = 0b010000000000;
    public const ALL           = 0b011111111111;

    public const INVALID  = 'notEmptyInvalid';
    public const IS_EMPTY = 'isEmpty';

    /**
     * @var array
     */
    protected $messageTemplates = [
        self::IS_EMPTY => 'Value is required and can\'t be empty',
        self::INVALID  => 'Invalid type given. String, integer, float, boolean or array expected',
    ];

    private $constants = [
        self::BOOLEAN       => 'boolean',
        self::INTEGER       => 'integer',
        self::FLOAT         => 'float',
        self::STRING        => 'string',
        self::ZERO          => 'zero',
        self::EMPTY_ARRAY   => 'array',
        self::NULL          => 'null',
        self::PHP           => 'php',
        self::SPACE         => 'space',
        self::OBJECT        => 'object',
        self::OBJECT_STRING => 'objectstring',
        self::OBJECT_COUNT  => 'objectcount',
        self::ALL           => 'all',
    ];

    /**
     * Default types allowed; value = 0b000111101001
     *
     * @var array
     */
    private $defaultTypeMask = [
        self::OBJECT,
        self::SPACE,
        self::NULL,
        self::EMPTY_ARRAY,
        self::STRING,
        self::BOOLEAN
    ];

    /**
     * @var int Type mask of allowed types that can represent empty values.
     */
    private $typeMask;

    /**
     * @param null|array|int|string $typeMask Allowed type(s) for representing
     *     not-empty values. Defaults to the mask OBJECT|SPACE|NULL|EMPTY_ARRAY|STRING|BOOLEAN.
     * @throws Exception\InvalidArgumentException if $typeMask is not null, an
     *     integer, a string, or an array.
     */
    public function __construct($typeMask = null)
    {
        if (null !== $typeMask
            && ! (is_int($typeMask) || is_string($typeMask) || is_array($typeMask))
        ) {
            throw new Exception\InvalidArgumentException(sprintf(
                '$typeMask argument to %s MUST be an integer corresponding to one of'
                . ' the type constants (or a mask of them), a string representation'
                . ' of one of those constants, or an array of such values; received %s',
                __CLASS__,
                is_object($typeMask) ? get_class($typeMask) : gettype($typeMask)
            ));
        }

        $this->typeMask = $this->calculateTypeMask($typeMask ?: $this->defaultTypeMask);
    }

    /**
     * {@inheritdoc}
     */
    public function validate($value, $context = null) : ResultInterface
    {
        if ($value !== null
            && ! is_string($value)
            && ! is_int($value)
            && ! is_float($value)
            && ! is_bool($value)
            && ! is_array($value)
            && ! is_object($value)
        ) {
            return $this->createInvalidResult($value, [self::INVALID]);
        }

        // NULL (null)
        if ($this->typeMask & self::NULL
            && null === $value
        ) {
            return $this->createInvalidResult($value, [self::IS_EMPTY]);
        }

        // BOOLEAN (false)
        if ($this->typeMask & self::BOOLEAN
            && $value === false
        ) {
            return $this->createInvalidResult($value, [self::IS_EMPTY]);
        }

        // EMPTY_ARRAY ([])
        if ($this->typeMask & self::EMPTY_ARRAY
            && $value === []
        ) {
            return $this->createInvalidResult($value, [self::IS_EMPTY]);
        }

        // FLOAT (0.0)
        if ($this->typeMask & self::FLOAT
            && $value === 0.0
        ) {
            return $this->createInvalidResult($value, [self::IS_EMPTY]);
        }

        // INTEGER (0)
        if ($this->typeMask & self::INTEGER
            && $value === 0
        ) {
            return $this->createInvalidResult($value, [self::IS_EMPTY]);
        }

        // SPACE|ZERO|STRING (' ', '0', '')
        if (is_string($value)) {
            return $this->validateStringValue($value);
        }

        // OBJECT|OBJECT_STRING|OBJECT_INT
        if (is_object($value)) {
            return $this->validateObjectValue($value);
        }

        return Result::createValidResult($value);
    }

    /**
     * @param array|int|string $typeMask
     */
    private function calculateTypeMask($typeMask) : int
    {
        if (is_array($typeMask)) {
            return $this->calculateTypeMaskFromArray($typeMask);
        }

        if (is_string($typeMask) && in_array($typeMask, $this->constants, true)) {
            return array_search($typeMask, $this->constants, true);
        }

        return $typeMask;
    }

    private function calculateTypeMaskFromArray(array $types) : int
    {
        $typeMask = 0;
        foreach ($types as $type) {
            if (is_int($type)) {
                $typeMask |= $type;
                continue;
            }

            if (in_array($type, $this->constants, true)) {
                $typeMask |= array_search($type, $this->constants, true);
                continue;
            }
        }
        return $typeMask;
    }

    /**
     * @param object $value
     */
    private function validateObjectValue($value) : ResultInterface
    {
        // Object-as-integer, but counting results in zero.
        if ($this->typeMask & self::OBJECT_COUNT
            && $value instanceof Countable
            && count($value) === 0
        ) {
            return $this->createInvalidResult($value, [self::IS_EMPTY]);
        }

        // Object-as-string, but results in empty string.
        if ($this->typeMask & self::OBJECT_STRING
            && (! method_exists($value, '__toString') || (string) $value === '')
        ) {
            return $this->createInvalidResult($value, [self::IS_EMPTY]);
        }

        // Objects (including countable and string represenatations) are
        // allowed; objects are never empty, so valid
        $objectMask = self::OBJECT | self::OBJECT_COUNT | self::OBJECT_STRING;
        if ($this->typeMask & $objectMask) {
            return Result::createValidResult($value);
        }

        // Objects are not allowed, but we have one.
        return $this->createInvalidResult($value, [self::IS_EMPTY]);
    }

    private function validateStringValue(string $value) : ResultInterface
    {
        if ($this->typeMask & self::SPACE
            && preg_match('/^\s+$/s', $value)
        ) {
            return $this->createInvalidResult($value, [self::IS_EMPTY]);
        }

        if ($this->typeMask & self::ZERO
            && $value === '0'
        ) {
            return $this->createInvalidResult($value, [self::IS_EMPTY]);
        }

        if ($this->typeMask & self::STRING
            && $value === ''
        ) {
            return $this->createInvalidResult($value, [self::IS_EMPTY]);
        }

        return Result::createValidResult($value);
    }
}
