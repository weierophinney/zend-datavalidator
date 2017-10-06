<?php
/**
 * @see       https://github.com/zendframework/zend-datavalidator for the canonical source repository
 * @copyright Copyright (c) 2017 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   https://github.com/zendframework/zend-datavalidator/blob/master/LICENSE.md New BSD License
 */

namespace Zend\DataValidator;

use DateTime;
use DateTimeImmutable;
use Traversable;

/**
 * Validates that a given value is a DateTime instance or can be converted into one.
 */
class Date extends AbstractValidator
{
    /**#@+
     * Validity constants
     * @var string
     */
    const INVALID        = self::class . 'dateInvalid';
    const INVALID_DATE   = self::class . 'dateInvalidDate';
    const FALSEFORMAT    = self::class . 'dateFalseFormat';
    /**#@-*/

    /**
     * Default format constant
     * @var string
     */
    const FORMAT_DEFAULT = 'Y-m-d';

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
    protected $format = self::FORMAT_DEFAULT;

    /**
     * Sets validator options
     *
     * @param string $format
     */
    public function __construct(string $format = null)
    {
        if (is_null($format)) {
            $format = self::FORMAT_DEFAULT;
        }

        $this->messageVariables = [
            'format' => $format,
        ];

        $this->format = $format;
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
     * Returns true if $value is a DateTime instance or can be converted into one.
     *
     * @param  string|array|int|DateTime $value
     * @return ResultInterface
     */
    public function validate($value, $context = null) : ResultInterface
    {
        $result = $this->convertToDateTime($value);

        if ($result instanceof ResultInterface) {
            return $result;
        }

        if (! $result) {
            return $this->createInvalidResult($value, [self::INVALID_DATE]);
        }

        return new Result(true, $value);
    }

    /**
     * Attempts to convert an int, string, or array to a DateTime object
     *
     * @param  string|int|array $param
     * @return bool|DateTime|ResultInterface
     */
    protected function convertToDateTime($param)
    {
        if ($param instanceof DateTime || $param instanceof DateTimeImmutable) {
            return $param;
        }

        $type = gettype($param);
        if (! in_array($type, ['string', 'integer', 'double', 'array'])) {
            return $this->createInvalidResult($param, [self::INVALID]);
        }

        $convertMethod = 'convert' . ucfirst($type);
        return $this->{$convertMethod}($param);
    }

    /**
     * Attempts to convert an integer into a DateTime object
     *
     * @param  integer $value
     * @return bool|DateTime
     */
    protected function convertInteger($value)
    {
        return date_create("@$value");
    }

    /**
     * Attempts to convert an double into a DateTime object
     *
     * @param  double $value
     * @return bool|DateTime
     */
    protected function convertDouble($value)
    {
        return DateTime::createFromFormat('U', $value);
    }

    /**
     * Attempts to convert a string into a DateTime object
     *
     * @param  string $value
     * @return ResultInterface|DateTime
     */
    protected function convertString($value)
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
     * @param  array $value
     * @return ResultInterface|DateTime
     * @todo   enhance the implosion
     */
    protected function convertArray(array $value)
    {
        return $this->convertString(implode('-', $value));
    }
}
