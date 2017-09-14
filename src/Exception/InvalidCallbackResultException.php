<?php
/**
 * @see       https://github.com/zendframework/zend-datavalidator for the canonical source repository
 * @copyright Copyright (c) 2017 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   https://github.com/zendframework/zend-datavalidator/blob/master/LICENSE.md New BSD License
 */

namespace Zend\DataValidator\Exception;

use RuntimeException;
use Zend\DataValidator\Callback;
use Zend\DataValidator\ResultInterface;

class InvalidCallbackResultException extends RuntimeException implements ExceptionInterface
{
    public static function forType($result) : self
    {
        return new self(sprintf(
            'Invalid result returned from %s; must be a %s instance or boolean; received %s',
            Callback::class,
            ResultInterface::class,
            is_object($result) ? get_class($result) : gettype($result)
        ));
    }
}
