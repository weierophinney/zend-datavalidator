<?php
/**
 * @see       https://github.com/zendframework/zend-datavalidator for the canonical source repository
 * @copyright Copyright (c) 2018 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   https://github.com/zendframework/zend-datavalidator/blob/master/LICENSE.md New BSD License
 */

namespace Zend\DataValidator\Barcode;

abstract class AbstractAdapter implements AdapterInterface
{
    /**
     * Allowed barcode characters
     */
    private $characters;

    /**
     * Allowed barcode lengths, integer, array, string
     */
    private $length;

    /**
     * Callback to checksum function
     */
    private $checksum;

    /**
     * Is a checksum value included?, boolean
     */
    private $useChecksum = true;

    /**
     * Checks the length of a barcode
     *
     * @param  string $value The barcode to check for proper length
     * @return bool
     */
    public function hasValidLength($value) : bool
    {
        if (! is_string($value)) {
            return false;
        }

        $fixum  = strlen($value);
        $found  = false;
        $length = $this->getLength();
        if (is_array($length)) {
            foreach ($length as $value) {
                if ($fixum == $value || $value == -1) {
                    return true;
                }
            }
        }

        if ($fixum == $length) {
            return true;
        }

        if ($length == -1) {
            return true;
        }

        if ($length == 'even') {
            $count = $fixum % 2;
            return (0 == $count);
        }

        if ($length == 'odd') {
            $count = $fixum % 2;
            return (1 == $count);
        }

        return false;
    }

    /**
     * Checks for allowed characters within the barcode
     *
     * @param  string $value The barcode to check for allowed characters
     * @return bool
     */
    public function hasValidCharacters($value) : bool
    {
        if (! is_string($value)) {
            return false;
        }

        $characters = $this->getCharacters();
        if ($characters == 128) {
            for ($x = 0; $x < 128; ++$x) {
                $value = str_replace(chr($x), '', $value);
            }
        } else {
            $chars = str_split($characters);
            foreach ($chars as $char) {
                $value = str_replace($char, '', $value);
            }
        }

        if (strlen($value) > 0) {
            return false;
        }

        return true;
    }

    /**
     * Validates the checksum
     *
     * @param  string $value The barcode to check the checksum for
     * @return bool
     */
    public function hasValidChecksum($value) : bool
    {
        $checksum = $this->getChecksum();
        if (! empty($checksum)) {
            if (method_exists($this, $checksum)) {
                return $this->$checksum($value);
            }
        }

        return false;
    }

    /**
     * Returns the allowed barcode length
     *
     * @return int|array|string
     */
    public function getLength()
    {
        return $this->length;
    }

    /**
     * Returns the allowed characters
     *
     * @return int|string|array
     */
    public function getCharacters()
    {
        return $this->characters;
    }

    /**
     * Returns the checksum function name
     *
     */
    public function getChecksum()
    {
        return $this->checksum;
    }

    /**
     * Sets the checksum validation method
     *
     * @param string $checksum Checksum method to call
     * @return AbstractAdapter
     */
    protected function setChecksum(string $checksum)
    {
        $this->checksum = $checksum;
        return $this;
    }

    /**
     * Sets the checksum validation setting
     *
     * @param  bool $check
     * @return null
     */
    public function setUseChecksum(bool $check) : void
    {
        $this->useChecksum = $check;
    }

    /**
     * Returns the checksum validation settings
     *
     * @return bool
     */
    public function useChecksum() : bool
    {
        return $this->useChecksum;
    }

    /**
     * Sets the length of this barcode
     *
     * @param  int|array|string $length
     * @return AbstractAdapter
     */
    protected function setLength($length)
    {
        $this->length = $length;
        return $this;
    }

    /**
     * Sets the allowed characters of this barcode
     *
     * @param int $characters
     * @return AbstractAdapter
     */
    protected function setCharacters($characters)
    {
        $this->characters = $characters;
        return $this;
    }

    /**
     * Validates the checksum (Modulo 10)
     * GTIN implementation factor 3
     *
     * @param  string $value The barcode to validate
     * @return bool
     */
    protected function gtin($value)
    {
        $barcode = substr($value, 0, -1);
        $sum     = 0;
        $length  = strlen($barcode) - 1;

        for ($i = 0; $i <= $length; $i++) {
            if (($i % 2) === 0) {
                $sum += $barcode[$length - $i] * 3;
            } else {
                $sum += $barcode[$length - $i];
            }
        }

        $calc     = $sum % 10;
        $checksum = ($calc === 0) ? 0 : (10 - $calc);
        if ($value[$length + 1] != $checksum) {
            return false;
        }

        return true;
    }

    /**
     * Validates the checksum (Modulo 10)
     * IDENTCODE implementation factors 9 and 4
     *
     * @param  string $value The barcode to validate
     * @return bool
     */
    protected function identcode($value)
    {
        $barcode = substr($value, 0, -1);
        $sum     = 0;
        $length  = strlen($value) - 2;

        for ($i = 0; $i <= $length; $i++) {
            if (($i % 2) === 0) {
                $sum += $barcode[$length - $i] * 4;
            } else {
                $sum += $barcode[$length - $i] * 9;
            }
        }

        $calc     = $sum % 10;
        $checksum = ($calc === 0) ? 0 : (10 - $calc);
        if ($value[$length + 1] != $checksum) {
            return false;
        }

        return true;
    }

    /**
     * Validates the checksum (Modulo 10)
     * CODE25 implementation factor 3
     *
     * @param  string $value The barcode to validate
     * @return bool
     */
    protected function code25($value)
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

    /**
     * Validates the checksum ()
     * POSTNET implementation
     *
     * @param  string $value The barcode to validate
     * @return bool
     */
    protected function postnet($value)
    {
        $checksum = substr($value, -1, 1);
        $values   = str_split(substr($value, 0, -1));

        $check = 0;
        foreach ($values as $row) {
            $check += $row;
        }

        $check %= 10;
        $check = 10 - $check;
        if ($check == $checksum) {
            return true;
        }

        return false;
    }
}
