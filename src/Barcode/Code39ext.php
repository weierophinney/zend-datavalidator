<?php
/**
 * @see       https://github.com/zendframework/zend-datavalidator for the canonical source repository
 * @copyright Copyright (c) 2018 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   https://github.com/zendframework/zend-datavalidator/blob/master/LICENSE.md New BSD License
 */

declare(strict_types=1);

namespace Zend\DataValidator\Barcode;

class Code39ext extends AbstractAdapter implements ChecksummableInterface
{
    use ChecksumTrait;
    use Code39ChecksumTrait;

    /**
     * Constructor for this barcode adapter
     */
    public function __construct(bool $useChecksum = false)
    {
        $this->checksumCallback = [$this, 'validateCode39Checksum'];
        $this->useChecksum = $useChecksum;
        $this->setLength(-1);
        $this->setCharacters(128);
    }
}
