<?php
/**
 * @see       https://github.com/zendframework/zend-datavalidator for the canonical source repository
 * @copyright Copyright (c) 2018 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   https://github.com/zendframework/zend-datavalidator/blob/master/LICENSE.md New BSD License
 */

declare(strict_types=1);

namespace Zend\DataValidator\Barcode;

class Code39 extends AbstractAdapter implements ChecksummableInterface
{
    use ChecksumTrait;

    /**
     * @var array
     */
    private $check = [
        '0' => 0,  '1' => 1,  '2' => 2,  '3' => 3,  '4' => 4,  '5' => 5,  '6' => 6,
        '7' => 7,  '8' => 8,  '9' => 9,  'A' => 10, 'B' => 11, 'C' => 12, 'D' => 13,
        'E' => 14, 'F' => 15, 'G' => 16, 'H' => 17, 'I' => 18, 'J' => 19, 'K' => 20,
        'L' => 21, 'M' => 22, 'N' => 23, 'O' => 24, 'P' => 25, 'Q' => 26, 'R' => 27,
        'S' => 28, 'T' => 29, 'U' => 30, 'V' => 31, 'W' => 32, 'X' => 33, 'Y' => 34,
        'Z' => 35, '-' => 36, '.' => 37, ' ' => 38, '$' => 39, '/' => 40, '+' => 41,
        '%' => 42,
    ];

    /**
     * Constructor for this barcode adapter
     */
    public function __construct(bool $useChecksum = false)
    {
        $this->setLength(-1);
        $this->setCharacters('0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ -.$/+%');
        $this->checksumCallback = [$this, 'validateCode39Checksum'];
        $this->useChecksum = $useChecksum;
    }

    /**
     * Validates the checksum (Modulo 43)
     */
    private function validateCode39Checksum(string $value) : bool
    {
        $checksum = substr($value, -1, 1);
        $value    = str_split(substr($value, 0, -1));
        $count    = 0;
        foreach ($value as $char) {
            $count += $this->check[$char];
        }

        $mod = $count % 43;
        if ($mod == $this->check[$checksum]) {
            return true;
        }

        return false;
    }
}
