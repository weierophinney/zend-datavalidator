<?php
/**
 * @see       https://github.com/zendframework/zend-datavalidator for the canonical source repository
 * @copyright Copyright (c) 2018 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   https://github.com/zendframework/zend-datavalidator/blob/master/LICENSE.md New BSD License
 */

declare(strict_types=1);

namespace Zend\DataValidator\Barcode;

class Issn extends AbstractAdapter implements ChecksummableInterface
{
    use ChecksumTrait;
    use GtinChecksumTrait;

    /**
     * Constructor for this barcode adapter
     */
    public function __construct(bool $useChecksum = true)
    {
        $this->useChecksum = $useChecksum;
        $this->setLength([8, 13]);
        $this->setCharacters('0123456789X');
    }

    /**
     * Allows X on length of 8 chars
     */
    public function hasValidCharacters(string $value) : bool
    {
        if (strlen($value) !== 8
            && strpos($value, 'X') !== false
        ) {
            return false;
        }

        return parent::hasValidCharacters($value);
    }

    /**
     * Validates the checksum
     *
     * Implements ChecksummableInterface::hasValidChecksum, and overrides
     * ChecksumTrait::hasValidChecksum.
     */
    public function hasValidChecksum(string $value) : bool
    {
        if (strlen($value) == 8) {
            return $this->validateIssnChecksum($value);
        }

        return $this->validateGtinChecksum($value);
    }

    /**
     * Validates the checksum ()
     * ISSN implementation (reversed mod11)
     */
    private function validateIssnChecksum(string $value) : bool
    {
        $checksum = substr($value, -1, 1);
        $values   = str_split(substr($value, 0, -1));
        $check    = 0;
        $multi    = 8;
        foreach ($values as $token) {
            if ($token === 'X') {
                $token = 10;
            }

            $check += (int) $token * $multi;
            $multi -= 1;
        }

        $check %= 11;
        $check = $check === 0 ? 0 : 11 - $check;

        return $check === (int) $checksum
            || ($check === 10 && $checksum === 'X');
    }
}
