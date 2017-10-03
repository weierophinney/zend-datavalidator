<?php
/**
 * @see       https://github.com/zendframework/zend-datavalidator for the canonical source repository
 * @copyright Copyright (c) 2017 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   https://github.com/zendframework/zend-datavalidator/blob/master/LICENSE.md New BSD License
 */

namespace Zend\DataValidator;

use Traversable;

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

    // Barcode adapter Zend\DataValidator\Barcode\AbstractAdapter
    private $adapter;

    private $useChecksum;

    public function __construct($adapter = null, bool $useChecksum = null)
    {
        $this->adapter = $adapter;
        $this->useChecksum = $useChecksum;

        $this->messageVariables = [
            'length' => null,
        ];
    }

    /**
     * Returns the set adapter
     *
     * @return Barcode\AbstractAdapter
     */
    public function getAdapter()
    {
        if (! ($this->adapter instanceof Barcode\AdapterInterface)) {
            $adapter = $this->adapter;
            if (is_null($adapter)) {
                $adapter = 'Ean13';
            }
            $this->setAdapter($adapter);
        }

        return $this->adapter;
    }

    /**
     * Sets a new barcode adapter
     *
     * @param  string|Barcode\AbstractAdapter $adapter Barcode adapter to use
     * @return Barcode
     * @throws Exception\InvalidArgumentException
     */
    public function setAdapter($adapter)
    {
        if (is_string($adapter)) {
            $adapter = ucfirst(strtolower($adapter));
            $adapter = 'Zend\\DataValidator\\Barcode\\' . $adapter;

            if (! class_exists($adapter)) {
                throw new Exception\InvalidArgumentException('Barcode adapter matching "' . $adapter . '" not found');
            }

            $adapter = new $adapter();

            if (! is_null($this->useChecksum)) {
                $adapter->useChecksum($this->useChecksum);
            }
        }

        if (! $adapter instanceof Barcode\AdapterInterface) {
            throw new Exception\InvalidArgumentException(
                sprintf(
                    "Adapter %s does not implement Zend\\DataValidator\\Barcode\\AdapterInterface",
                    (is_object($adapter) ? get_class($adapter) : gettype($adapter))
                )
            );
        }

        $this->adapter = $adapter;

        return $this;
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
     * Sets if checksum should be validated, if no value is given the actual setting is returned
     *
     * @param  bool $checksum
     * @return bool
     */
    public function useChecksum($checksum = null)
    {
        return $this->getAdapter()->useChecksum($checksum);
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

        if ($this->useChecksum(null)) {
            $result = $adapter->hasValidChecksum($value);
            if (! $result) {
                return $this->createInvalidResult($value, [self::FAILED]);
            }
        }

        return new Result(true, $value);
    }
}
