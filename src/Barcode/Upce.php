<?php
/**
 * @see       https://github.com/zendframework/zend-datavalidator for the canonical source repository
 * @copyright Copyright (c) 2018 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   https://github.com/zendframework/zend-datavalidator/blob/master/LICENSE.md New BSD License
 */

declare(strict_types=1);

namespace Zend\DataValidator\Barcode;

class Upce extends AbstractAdapter implements ChecksummableInterface
{
    use ChecksumTrait;
    use GtinChecksumTrait;

    /**
     * Constructor for this barcode adapter
     */
    public function __construct()
    {
        $this->checksumCallback = [$this, 'validateGtinChecksum'];
        $this->setLength([6, 7, 8]);
        $this->setCharacters('0123456789');
    }

    /**
     * Implements ChecksummableInterface::useChecksum, and overrides
     * ChecksumTrait::useChecksum
     */
    public function useChecksum(string $value = null) : bool
    {
        return strlen($value) === 8;
    }
}
