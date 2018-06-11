<?php
/**
 * @see       https://github.com/zendframework/zend-datavalidator for the canonical source repository
 * @copyright Copyright (c) 2018 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   https://github.com/zendframework/zend-datavalidator/blob/master/LICENSE.md New BSD License
 */

namespace Zend\DataValidator\Barcode;

class Upce extends AbstractAdapter
{
    /**
     * Constructor for this barcode adapter
     */
    public function __construct()
    {
        $this->setLength([6, 7, 8]);
        $this->setCharacters('0123456789');
        $this->setChecksum('gtin');
    }

    /**
     * Overrides parent checkLength
     *
     * @param string $value Value
     * @return bool
     */
    public function hasValidLength($value) : bool
    {
        $this->setUseChecksum(strlen($value) === 8);

        return parent::hasValidLength($value);
    }
}
