<?php
/**
 * @see       https://github.com/zendframework/zend-datavalidator for the canonical source repository
 * @copyright Copyright (c) 2017 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   https://github.com/zendframework/zend-datavalidator/blob/master/LICENSE.md New BSD License
 */

namespace Zend\DataValidator;

use Traversable;
use Zend\DataValidator\Barcode\AdapterInterface;

class Barcode extends AbstractValidator
{
    const INVALID        = self::class . '::barcodeInvalid';
    const FAILED         = self::class . '::barcodeFailed';
    const INVALID_CHARS  = self::class . '::barcodeInvalidChars';
    const INVALID_LENGTH = self::class . '::barcodeInvalidLength';

    protected $messageTemplates = [
        self::FAILED         => "The input failed checksum validation",
        self::INVALID_CHARS  => "The input contains invalid characters",
        self::INVALID_LENGTH => "The input should have a length of %length% characters",
        self::INVALID        => "Invalid type given. String expected",
    ];

    /**
     * Barcode adapter Zend\DataValidator\Barcode\AdapterInterface
     */
    private $adapter;

    /**
     * @param $adapter     AdapterInterface  Barcode adapter
     */
    public function __construct(AdapterInterface $adapter)
    {
        $this->adapter = $adapter;

        $this->messageVariables = [
            'length' => null,
        ];
    }

    /**
     * Returns the set adapter
     *
     * @return AdapterInterface
     */
    public function getAdapter() : AdapterInterface
    {
        return $this->adapter;
    }

    /**
     * Returns the checksum option
     *
     * @return string
     */
    public function getChecksum()
    {
        return $this->getAdapter()->getChecksum();
    }

    /**
     * Sets the checksum validation
     *
     * @param  bool $check
     * @return void
     */
    public function setUseChecksum(bool $check) : void
    {
        $this->getAdapter()->setUseChecksum($check);
    }

    /**
     * Returns the actual setting of checksum
     *
     * @return bool
     */
    public function useChecksum() : bool
    {
        return $this->getAdapter()->useChecksum();
    }

    /**
     * Validate the value is a correct barcode.
     *
     * @return ResultInterface
     */
    public function validate($value, $context = null) : ResultInterface
    {
        if (! is_string($value)) {
            return $this->createInvalidResult($value, [self::INVALID]);
        }

        $adapter = $this->getAdapter();
        $length  = $adapter->getLength();
        $result  = $adapter->hasValidLength($value);
        if (! $result) {
            if (is_array($length)) {
                $temp = $length;
                $length = "";
                foreach ($temp as $len) {
                    $length .= "/";
                    $length .= $len;
                }

                $length = substr($length, 1);
            }

            $this->messageVariables['length'] = $length;

            return $this->createInvalidResult($value, [self::INVALID_LENGTH]);
        }

        $result = $adapter->hasValidCharacters($value);
        if (! $result) {
            return $this->createInvalidResult($value, [self::INVALID_CHARS]);
        }

        if ($this->useChecksum()) {
            $result = $adapter->hasValidChecksum($value);
            if (! $result) {
                return $this->createInvalidResult($value, [self::FAILED]);
            }
        }

        return new Result(true, $value);
    }
}
