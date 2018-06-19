<?php
/**
 * @see       https://github.com/zendframework/zend-datavalidator for the canonical source repository
 * @copyright Copyright (c) 2018 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   https://github.com/zendframework/zend-datavalidator/blob/master/LICENSE.md New BSD License
 */

declare(strict_types=1);

namespace Zend\DataValidator\Barcode;

class Royalmail extends AbstractAdapter implements ChecksummableInterface
{
    use ChecksumTrait;

    /**
     * @var int[]
     */
    private $rows = [
        '0' => 1, '1' => 1, '2' => 1, '3' => 1, '4' => 1, '5' => 1,
        '6' => 2, '7' => 2, '8' => 2, '9' => 2, 'A' => 2, 'B' => 2,
        'C' => 3, 'D' => 3, 'E' => 3, 'F' => 3, 'G' => 3, 'H' => 3,
        'I' => 4, 'J' => 4, 'K' => 4, 'L' => 4, 'M' => 4, 'N' => 4,
        'O' => 5, 'P' => 5, 'Q' => 5, 'R' => 5, 'S' => 5, 'T' => 5,
        'U' => 0, 'V' => 0, 'W' => 0, 'X' => 0, 'Y' => 0, 'Z' => 0,
     ];

    /**
     * @var int[]
     */
    private $columns = [
        '0' => 1, '1' => 2, '2' => 3, '3' => 4, '4' => 5, '5' => 0,
        '6' => 1, '7' => 2, '8' => 3, '9' => 4, 'A' => 5, 'B' => 0,
        'C' => 1, 'D' => 2, 'E' => 3, 'F' => 4, 'G' => 5, 'H' => 0,
        'I' => 1, 'J' => 2, 'K' => 3, 'L' => 4, 'M' => 5, 'N' => 0,
        'O' => 1, 'P' => 2, 'Q' => 3, 'R' => 4, 'S' => 5, 'T' => 0,
        'U' => 1, 'V' => 2, 'W' => 3, 'X' => 4, 'Y' => 5, 'Z' => 0,
    ];

    /**
     * Constructor for this barcode adapter
     */
    public function __construct(bool $useChecksum = true)
    {
        $this->useChecksum = $useChecksum;
        $this->checksumCallback = [$this, 'validateRoyalmailChecksum'];
        $this->setLength(-1);
        $this->setCharacters('0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ');
    }

    /**
     * Allows start and stop tag within checked chars
     */
    public function hasValidCharacters(string $value) : bool
    {
        if ($value[0] === '(') {
            if ($value[strlen($value) - 1] !== ')') {
                return false;
            }

            $value = substr($value, 1, -1);
        }

        return parent::hasValidCharacters($value);
    }

    /**
     * Validates the checksum
     */
    private function validateRoyalmailChecksum(string $value) : bool
    {
        $checksum = substr($value, -1, 1);
        $values   = str_split(substr($value, 0, -1));
        $rowvalue = 0;
        $colvalue = 0;
        foreach ($values as $row) {
            $rowvalue += $this->rows[$row];
            $colvalue += $this->columns[$row];
        }

        $rowvalue %= 6;
        $colvalue %= 6;

        $rowchkvalue = array_keys($this->rows, $rowvalue);
        $colchkvalue = array_keys($this->columns, $colvalue);
        $intersect   = array_intersect($rowchkvalue, $colchkvalue);
        $chkvalue    = current($intersect);
        return $chkvalue === $checksum;
    }
}
