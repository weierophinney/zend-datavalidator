<?php
/**
 * @see       https://github.com/zendframework/zend-datavalidator for the canonical source repository
 * @copyright Copyright (c) 2018 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   https://github.com/zendframework/zend-datavalidator/blob/master/LICENSE.md New BSD License
 */

declare(strict_types=1);

namespace Zend\DataValidator;

use DateTime;
use DateTimeInterface;
use Traversable;

use function date_create;

/**
 * Validates that a given value is a DateTime instance or can be converted into one.
 */
class Date extends AbstractValidator
{
    /**#@+
     * Validity constants
     * @var string
     */
    public const INVALID        = self::class . 'invalidValue';
    public const INVALID_DATE   = self::class . 'invalidDate';
    public const FALSEFORMAT    = self::class . 'falseFormat';
    /**#@-*/

    /**
     * Default format constant
     * @var string
     */
    public const FORMAT_DEFAULT = 'Y-m-d';

    /**
     * Validation failure message template definitions
     *
     * @var array
     */
    protected $messageTemplates = [
        self::INVALID      => "Invalid type given. String, integer, array or DateTime expected",
        self::INVALID_DATE => "The input does not appear to be a valid date",
        self::FALSEFORMAT  => "The input does not fit the date format '%format%'",
    ];

    /**
     * @var string
     */
    private $format = self::FORMAT_DEFAULT;

    public function __construct(string $format = null)
    {
        $this->format = $format ?: self::FORMAT_DEFAULT;

        $this->messageVariables = [
            'format' => $this->format,
        ];
    }

    /**
     * Returns the format option
     *
     * @return string
     */
    public function getFormat()
    {
        return $this->format;
    }

    /**
     * Returns a successful validation result if $value is a DateTimeImmutable
     * instance or can be converted into one using the format provided during
     * instantiation.
     *
     * @param string|array|int|float|DateTimeImmutable $value
     */
    public function validate($value, $context = null) : ResultInterface
    {
        if ($value instanceof DateTimeInterface) {
            return Result::createValidResult($value);
        }

        switch (gettype($value)) {
            case 'string':
                $result = $this->convertString($value);
                break;
            case 'integer':
                $result = $this->convertInteger($value);
                break;
            case 'double':
                $result = $this->convertDouble($value);
                break;
            case 'array':
                $result = $this->convertArray($value);
                break;
            default:
                return $this->createInvalidResult($value, [self::INVALID]);
        }

        if ($result instanceof ResultInterface) {
            return $result;
        }

        if (false === $result) {
            return $this->createInvalidResult($value, [self::INVALID_DATE]);
        }

        return Result::createValidResult($value);
    }

    /**
     * Attempts to convert an integer into a DateTime object
     *
     * @return false|DateTime
     */
    private function convertInteger(int $value)
    {
        return date_create("@$value");
    }

    /**
     * Attempts to convert an double into a DateTime object
     *
     * @return false|DateTime
     */
    private function convertDouble(float $value)
    {
        return DateTime::createFromFormat('U', (string) $value);
    }

    /**
     * Attempts to convert a string into a DateTime object
     *
     * @param  string $value
     * @return false|ResultInterface|DateTime
     */
    private function convertString(string $value)
    {
        $date = DateTime::createFromFormat($this->format, $value);

        // Invalid dates can show up as warnings (ie. "2007-02-99")
        // and still return a DateTime object.
        $errors = DateTime::getLastErrors();
        if ($errors['warning_count'] > 0) {
            return $this->createInvalidResult($value, [self::FALSEFORMAT]);
        }

        return $date;
    }

    /**
     * Implodes the array into a string and proxies to {@link convertString()}.
     *
     * @return false|ResultInterface|DateTime
     * @todo   enhance the implosion
     */
    private function convertArray(array $value)
    {
        return $this->convertString(implode('-', $value));
    }
}
