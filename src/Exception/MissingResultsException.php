<?php
/**
 * @see       https://github.com/zendframework/zend-datavalidator for the canonical source repository
 * @copyright Copyright (c) 2017 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   https://github.com/zendframework/zend-datavalidator/blob/master/LICENSE.md New BSD License
 */

declare(strict_types=1);

namespace Zend\DataValidator\Exception;

use RuntimeException;
use Zend\DataValidator\ResultInterface;

class MissingResultsException extends RuntimeException implements ExceptionInterface
{
    public static function forClass(string $className) : self
    {
        return new self(sprintf(
            '%s is missing instaces of %s, and is thus in an ambiguous state',
            $className,
            ResultInterface::class
        ));
    }
}
