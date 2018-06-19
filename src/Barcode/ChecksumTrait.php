<?php
/**
 * @see       https://github.com/zendframework/zend-datavalidator for the canonical source repository
 * @copyright Copyright (c) 2018 Zend Technologies USA Inc. (https://www.zend.com)
 * @license   https://github.com/zendframework/zend-datavalidator/blob/master/LICENSE.md New BSD License
 */

declare(strict_types=1);

namespace Zend\DataValidator\Barcode;

trait ChecksumTrait
{
    /**
     * @var callable
     */
    private $checksumCallback;

    /**
     * @var bool
     */
    private $useChecksum = false;

    /**
     * Whether or not the current instance is using a checksum.
     *
     * @param null|string $value Whether or not to validate using a checksum
     *     for the current value. The trait ignores $value; implementations
     *     using this trait can override it to do tests on the $value if
     *     necessary.
     */
    public function useChecksum(string $value = null) : bool
    {
        return $this->useChecksum;
    }

    /**
     * Whether or not the checksum of the value is valid.
     *
     * @param mixed $value
     */
    public function hasValidChecksum(string $value) : bool
    {
        if (! is_callable($this->checksumCallback)) {
            return false;
        }

        $validator = $this->checksumCallback;

        return $validator($value);
    }
}
