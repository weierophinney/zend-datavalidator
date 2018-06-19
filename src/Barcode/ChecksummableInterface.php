<?php
/**
 * @see       https://github.com/zendframework/zend-datavalidator for the canonical source repository
 * @copyright Copyright (c) 2018 Zend Technologies USA Inc. (https://www.zend.com)
 * @license   https://github.com/zendframework/zend-datavalidator/blob/master/LICENSE.md New BSD License
 */

declare(strict_types=1);

namespace Zend\DataValidator\Barcode;

interface ChecksummableInterface
{
    /**
     * Whether or not the current instance is using a checksum.
     */
    public function useChecksum(string $value = null) : bool;

    /**
     * Whether or not the checksum of the value is valid.
     */
    public function hasValidChecksum(string $value) : bool;
}
