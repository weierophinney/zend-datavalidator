<?php
/**
 * @see       https://github.com/zendframework/zend-datavalidator for the canonical source repository
 * @copyright Copyright (c) 2017 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   https://github.com/zendframework/zend-datavalidator/blob/master/LICENSE.md New BSD License
 */

namespace Zend\DataValidator;

/**
 * Allow delegating validation to a callable.
 *
 * The callable provided to the constructor may return either a
 * `ResultInterface` instance, or a boolean value. In the latter case, if the
 * value does not pass validation, the `self::INVALID` message template will be
 * used to create a validation failure message for the returned result.
 */
class Callback extends AbstractValidator
{
    const INVALID = self::class . '::invalid';

    /**
     * Validation failure message template definitions
     *
     * @var array
     */
    protected $messageTemplates = [
        self::INVALID => 'Value does not pass custom validation',
    ];

    /**
     * @var callable
     */
    private $callback;

    public function __construct(callable $callback)
    {
        $this->callback = $callback;
    }

    public function validate($value, $context = null) : ResultInterface
    {
        $result = ($this->callback)($value, $context);

        if ($result instanceof ResultInterface) {
            return $result;
        }

        if (! is_bool($result)) {
            throw Exception\InvalidCallbackResultException::forType($result);
        }

        return $result
            ? new Result(true, $value)
            : $this->createInvalidResult($value, [self::INVALID]);
    }
}
