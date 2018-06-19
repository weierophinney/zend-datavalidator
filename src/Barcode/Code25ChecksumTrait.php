<?php
/**
 * @see       https://github.com/zendframework/zend-datavalidator for the canonical source repository
 * @copyright Copyright (c) 2018 Zend Technologies USA Inc. (https://www.zend.com)
 * @license   https://github.com/zendframework/zend-datavalidator/blob/master/LICENSE.md New BSD License
 */

declare(strict_types=1);

namespace Zend\DataValidator\Barcode;

trait Code25ChecksumTrait
{
    /**
     * Validates the checksum (Modulo 10)
     * CODE25 implementation factor 3
     */
    private function validateCode25Checksum(string $value) : bool
    {
        $barcode = substr($value, 0, -1);
        $sum     = 0;
        $length  = strlen($barcode) - 1;

        for ($i = 0; $i <= $length; $i++) {
            if (($i % 2) === 0) {
                $sum += $barcode[$i] * 3;
            } else {
                $sum += $barcode[$i];
            }
        }

        $calc     = $sum % 10;
        $checksum = ($calc === 0) ? 0 : (10 - $calc);
        if ($value[$length + 1] != $checksum) {
            return false;
        }

        return true;
    }
}
