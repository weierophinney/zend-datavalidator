<?php
/**
 * @see       https://github.com/zendframework/zend-datavalidator for the canonical source repository
 * @copyright Copyright (c) 2017 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   https://github.com/zendframework/zend-datavalidator/blob/master/LICENSE.md New BSD License
 */

declare(strict_types=1);

namespace Zend\DataValidator;

interface ValidatorInterface
{
    /**
     * @param mixed $value Value to validate
     * @param object|array|mixed $context Validation context (e.g., set of
     *     other form elements)
     */
    public function validate($value, $context = null) : ResultInterface;
}
