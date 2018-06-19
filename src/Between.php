<?php
/**
 * @see       https://github.com/zendframework/zend-datavalidator for the canonical source repository
 * @copyright Copyright (c) 2017 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   https://github.com/zendframework/zend-datavalidator/blob/master/LICENSE.md New BSD License
 */

namespace Zend\DataValidator;

final class Between extends AbstractValidator
{
    public const NOT_BETWEEN        = self::class . '::notBetween';
    public const NOT_BETWEEN_STRICT = self::class . '::notBetweenStrict';

    /**
     * Validation failure message template definitions
     *
     * @var array
     */
    protected $messageTemplates = [
        self::NOT_BETWEEN        => 'The input is not between "%min%" and "%max%", inclusively',
        self::NOT_BETWEEN_STRICT => 'The input is not strictly between "%min%" and "%max%"',
    ];

    /**
     * @var bool
     */
    private $inclusive;

    /**
     * @var int|float
     */
    private $max;

    /**
     * @var int|float
     */
    private $min;

    /**
     * Sets validator options
     * Accepts the following option keys:
     *   'min' => scalar, minimum border
     *   'max' => scalar, maximum border
     *   'inclusive' => boolean, inclusive border values
     *
     * @param int|float $min
     * @param int|float $max
     * @throws Exception\InvalidArgumentException if $min is not numeric
     * @throws Exception\InvalidArgumentException if $max is not numeric
     */
    public function __construct($min = 0, $max = PHP_INT_MAX, bool $inclusive = true)
    {
        if (! is_numeric($min)) {
            throw new Exception\InvalidArgumentException(sprintf(
                'Invalid value for "min"; must be numeric, received %s',
                is_object($min) ? get_class($min) : gettype($min)
            ));
        }
        if (! is_numeric($max)) {
            throw new Exception\InvalidArgumentException(sprintf(
                'Invalid value for "max"; must be numeric, received %s',
                is_object($max) ? get_class($max) : gettype($max)
            ));
        }

        $this->min = $min;
        $this->max = $max;
        $this->inclusive = $inclusive;

        $this->messageVariables = [
            'min' => $min,
            'max' => $max,
        ];
    }

    /**
     * Returns the min option
     *
     * @return int|float
     */
    public function getMin()
    {
        return $this->min;
    }

    /**
     * Returns the max option
     *
     * @return int|float
     */
    public function getMax()
    {
        return $this->max;
    }

    public function isInclusive() : bool
    {
        return $this->inclusive;
    }

    /**
     * Returns true if and only if $value is between min and max options, inclusively
     * if inclusive option is true.
     */
    public function validate($value, $context = null) : ResultInterface
    {
        return $this->isInclusive()
            ? $this->validateInclusive($value, $context)
            : $this->validateExclusive($value, $context);
    }

    private function validateInclusive($value, $context) : Result
    {
        if ($value < $this->getMin() || $value > $this->getMax()) {
            return $this->createInvalidResult($value, [self::NOT_BETWEEN]);
        }
        return Result::createValidResult($value);
    }

    private function validateExclusive($value, $context) : Result
    {
        if ($value <= $this->getMin() || $value >= $this->getMax()) {
            return $this->createInvalidResult($value, [self::NOT_BETWEEN_STRICT]);
        }
        return Result::createValidResult($value);
    }
}
