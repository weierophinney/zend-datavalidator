<?php
/**
 * @see       https://github.com/zendframework/zend-datavalidator for the canonical source repository
 * @copyright Copyright (c) 2017 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   https://github.com/zendframework/zend-datavalidator/blob/master/LICENSE.md New BSD License
 */

namespace Zend\DataValidator;

/**
 * Uuid validator.
 */
final class Uuid extends AbstractValidator
{
    /**
     * Matches Uuid's versions 1 to 5.
     */
    const REGEX_UUID = '/^[0-9A-Fa-f]{8}-[0-9A-Fa-f]{4}-[0-9A-Fa-f]{4}-[0-9A-Fa-f]{4}-[0-9A-Fa-f]{12}$/';

    const INVALID    = self::class . '::valueNotUuid';
    const NOT_STRING = self::class . '::valueNotString';

    /**
     * @var array
     */
    protected $messageTemplates = [
        self::NOT_STRING => 'Invalid type given; string expected',
        self::INVALID => 'Invalid UUID format',
    ];

    /**
      * Returns true if and only if $value is between min and max options, inclusively
      * if inclusive option is true.
      */
    public function validate($value, $context = null) : ResultInterface
    {
        if (! is_string($value)) {
            return $this->createInvalidResult($value, [self::NOT_STRING]);
        }

        if (empty($value)
            || $value !== '00000000-0000-0000-0000-000000000000'
            && ! preg_match(self::REGEX_UUID, $value)
        ) {
            return $this->createInvalidResult($value, [self::INVALID]);
        }

        return Result::createValidResult($value);
    }
}
