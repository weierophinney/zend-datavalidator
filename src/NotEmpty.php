<?php
/**
 * @see       https://github.com/zendframework/zend-datavalidator for the canonical source repository
 * @copyright Copyright (c) 2017 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   https://github.com/zendframework/zend-datavalidator/blob/master/LICENSE.md New BSD License
 */

namespace Zend\DataValidator;

final class NotEmpty extends AbstractValidator
{
    const BOOLEAN       = 0b000000000001;
    const INTEGER       = 0b000000000010;
    const FLOAT         = 0b000000000100;
    const STRING        = 0b000000001000;
    const ZERO          = 0b000000010000;
    const EMPTY_ARRAY   = 0b000000100000;
    const NULL          = 0b000001000000;
    const PHP           = 0b000001111111;
    const SPACE         = 0b000010000000;
    const OBJECT        = 0b000100000000;
    const OBJECT_STRING = 0b001000000000;
    const OBJECT_COUNT  = 0b010000000000;
    const ALL           = 0b011111111111;

    const INVALID  = 'notEmptyInvalid';
    const IS_EMPTY = 'isEmpty';

    protected $constants = [
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
     * Default value for types; value = 0b000111101001
     *
     * @var array
     */
    protected $defaultType = [
        self::OBJECT,
        self::SPACE,
        self::NULL,
        self::EMPTY_ARRAY,
        self::STRING,
        self::BOOLEAN
    ];

    /**
     * @var array
     */
    protected $messageTemplates = [
        self::IS_EMPTY => 'Value is required and can\'t be empty',
        self::INVALID  => 'Invalid type given. String, integer, float, boolean or array expected',
    ];

    /**
     * @param array|int|string $type
     * @return int
     */
    protected function calculateTypeValue($type): int
    {
        if (\is_array($type)) {
            $detected = 0;
            foreach ($type as $value) {
                if (\is_int($value)) {
                    $detected |= $value;
                } elseif (\in_array($value, $this->constants, true)) {
                    $detected |= \array_search($value, $this->constants, true);
                }
            }

            $type = $detected;
        } elseif (\is_string($type) && \in_array($type, $this->constants, true)) {
            $type = \array_search($type, $this->constants, true);
        }

        return $type;
    }

    /**
     * {@inheritdoc}
     */
    public function validate($value, $context = null): ResultInterface
    {
        if ($value !== null && ! \is_string($value) && ! \is_int($value) && ! \is_float($value) &&
            ! \is_bool($value) && ! \is_array($value) && ! \is_object($value)
        ) {
            return $this->createInvalidResult($value, [self::INVALID]);
        }

        $type   = $this->calculateTypeValue($value);
        $object = false;

        // OBJECT_COUNT (countable object)
        if ($type & self::OBJECT_COUNT) {
            $object = true;

            if (\is_object($value) && ($value instanceof \Countable) && (\count($value) === 0)) {
                return $this->createInvalidResult($value,[self::IS_EMPTY]);
            }
        }

        // OBJECT_STRING (object's toString)
        if ($type & self::OBJECT_STRING) {
            $object = true;

            if ((\is_object($value) && (! method_exists($value, '__toString'))) ||
                (\is_object($value) && method_exists($value, '__toString') && (((string) $value) === ''))) {
                return $this->createInvalidResult($value, [self::IS_EMPTY]);
            }
        }

        // OBJECT (object)
        if ($type & self::OBJECT) {
            // fall trough, objects are always not empty
        } elseif ($object === false) {
            // object not allowed but object given -> return false
            if (\is_object($value)) {
                return $this->createInvalidResult($value, [self::IS_EMPTY]);
            }
        }

        // SPACE ('   ')
        if ($type & self::SPACE) {
            if (\is_string($value) && preg_match('/^\s+$/s', $value)) {
                return $this->createInvalidResult($value, [self::IS_EMPTY]);
            }
        }

        // NULL (null)
        if ($type & self::NULL) {
            if ($value === null) {
                return $this->createInvalidResult($value, [self::IS_EMPTY]);
            }
        }

        // EMPTY_ARRAY (array())
        if ($type & self::EMPTY_ARRAY) {
            if (\is_array($value) && ($value === [])) {
                return $this->createInvalidResult($value, [self::IS_EMPTY]);
            }
        }

        // ZERO ('0')
        if ($type & self::ZERO) {
            if (\is_string($value) && ($value === '0')) {
                return $this->createInvalidResult($value, [self::IS_EMPTY]);
            }
        }

        // STRING ('')
        if ($type & self::STRING) {
            if (\is_string($value) && ($value === '')) {
                return $this->createInvalidResult($value, [self::IS_EMPTY]);
            }
        }

        // FLOAT (0.0)
        if ($type & self::FLOAT) {
            if (\is_float($value) && ($value === 0.0)) {
                return $this->createInvalidResult($value, [self::IS_EMPTY]);
            }
        }

        // INTEGER (0)
        if ($type & self::INTEGER) {
            if (\is_int($value) && ($value === 0)) {
                return $this->createInvalidResult($value, [self::IS_EMPTY]);
            }
        }

        // BOOLEAN (false)
        if ($type & self::BOOLEAN) {
            if (\is_bool($value) && ($value === false)) {
                return $this->createInvalidResult($value, [self::IS_EMPTY]);
            }
        }

        return new Result(true, $value);
    }
}
