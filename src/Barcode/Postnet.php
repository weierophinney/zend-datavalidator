<?php
/**
 * @see       https://github.com/zendframework/zend-datavalidator for the canonical source repository
 * @copyright Copyright (c) 2018 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   https://github.com/zendframework/zend-datavalidator/blob/master/LICENSE.md New BSD License
 */

declare(strict_types=1);

namespace Zend\DataValidator\Barcode;

class Postnet extends AbstractAdapter implements ChecksummableInterface
{
    use ChecksumTrait;
    use PostnetChecksumTrait;

    /**
     * Constructor for this barcode adapter
     */
    public function __construct(bool $useChecksum = true)
    {
        $this->useChecksum = $useChecksum;
        $this->checksumCallback = [$this, 'validatePostnetChecksum'];
        $this->setLength([6, 7, 10, 12]);
        $this->setCharacters('0123456789');
    }
}
