<?php
/**
 * @see       https://github.com/zendframework/zend-datavalidator for the canonical source repository
 * @copyright Copyright (c) 2018 Zend Technologies USA Inc. (https://www.zend.com)
 * @license   https://github.com/zendframework/zend-datavalidator/blob/master/LICENSE.md New BSD License
 */

declare(strict_types=1);

namespace Zend\DataValidator\Barcode;

trait IdentcodeChecksumTrait
{
    /**
     * Validates the checksum (Modulo 10)
     * IDENTCODE implementation factors 9 and 4
     */
    private function validateIdentcodeChecksum(string $value) : bool
    {
        $barcode = substr($value, 0, -1);
        $sum     = 0;
        $length  = strlen($value) - 2;

        for ($i = 0; $i <= $length; $i++) {
            $digit = (int) $barcode[$length - $i];
            if ($i % 2 === 0) {
                $sum += $digit * 4;
                continue;
            }
            $sum += $digit * 9;
        }

        $calc     = $sum % 10;
        $checksum = $calc === 0 ? 0 : 10 - $calc;
        $expected = (int) $value[$length + 1];
        return $expected === $checksum;
    }
}
