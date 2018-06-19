<?php
/**
 * @see       https://github.com/zendframework/zend-datavalidator for the canonical source repository
 * @copyright Copyright (c) 2018 Zend Technologies USA Inc. (https://www.zend.com)
 * @license   https://github.com/zendframework/zend-datavalidator/blob/master/LICENSE.md New BSD License
 */

declare(strict_types=1);

namespace Zend\DataValidator\Barcode;

trait PostnetChecksumTrait
{
    /**
     * Validates the checksum ()
     * POSTNET implementation
     */
    private function validatePostnetChecksum(string $value) : bool
    {
        $checksum = (int) substr($value, -1, 1);
        $values   = str_split(substr($value, 0, -1));

        $check = 0;
        foreach ($values as $row) {
            $check += (int) $row;
        }

        $check %= 10;
        $check = 10 - $check;
        return $check === $checksum;
    }
}
