<?php
/**
 * @see       https://github.com/zendframework/zend-datavalidator for the canonical source repository
 * @copyright Copyright (c) 2018 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   https://github.com/zendframework/zend-datavalidator/blob/master/LICENSE.md New BSD License
 */

declare(strict_types=1);

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
     * Checks the length of a barcode
     *
     * @param string $value The barcode to check for proper length
     * @return bool
     */
    public function hasValidLength(string $value) : bool
    {
        $allowedLength = $this->getLength();

        if ($allowedLength === -1) {
            return true;
        }

        $valueLength = strlen($value);

        if ($valueLength === $allowedLength) {
            return true;
        }

        if ($allowedLength === 'even') {
            $count = $valueLength % 2;
            return 0 === $count;
        }

        if ($allowedLength === 'odd') {
            $count = $valueLength % 2;
            return 1 === $count;
        }

        if (is_array($allowedLength)) {
            return array_reduce($allowedLength, function ($allowed, $test) use ($valueLength) {
                return $allowed
                    || $test === -1
                    || $valueLength === $test;
            }, false);
        }

        return false;
    }

    /**
     * Checks for allowed characters within the barcode
     */
    public function hasValidCharacters(string $value) : bool
    {
        $characters = $this->getCharacters();
        $value = $characters === 128
            ? $this->replaceViaChr($value)
            : $this->replaceViaCharMap($value, (string) $characters);

        return strlen($value) === 0;
    }

    /**
     * Returns the allowed barcode length
     *
     * @return int|string|array
     */
    public function getLength()
    {
        return $this->length;
    }

    /**
     * Returns the allowed characters
     *
     * @return int
     */
    public function getCharacters()
    {
        return $this->characters;
    }

    /**
     * Sets the length of this barcode
     *
     * @param int|string|array $length
     */
    protected function setLength($length) : void
    {
        $this->length = $length;
    }

    /**
     * Sets the allowed characters of this barcode
     *
     * @param int|array|string $characters
     */
    protected function setCharacters($characters) : void
    {
        $this->characters = $characters;
    }

    private function replaceViaChr(string $value) : string
    {
        for ($x = 0; $x < 128; $x += 1) {
            $value = str_replace(chr($x), '', $value);
        }
        return $value;
    }

    private function replaceViaCharMap(string $value, string $characters) : string
    {
        foreach (str_split($characters) as $char) {
            $value = str_replace($char, '', $value);
        }
        return $value;
    }
}
