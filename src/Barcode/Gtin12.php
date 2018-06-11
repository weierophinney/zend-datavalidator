<?php
/**
 * @see       https://github.com/zendframework/zend-datavalidator for the canonical source repository
 * @copyright Copyright (c) 2018 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   https://github.com/zendframework/zend-datavalidator/blob/master/LICENSE.md New BSD License
 */

namespace Zend\DataValidator\Barcode;

class Gtin12 extends AbstractAdapter
{
    /**
     * Constructor for this barcode adapter
     */
    public function __construct()
    {
        $this->setLength(12);
        $this->setCharacters('0123456789');
        $this->setChecksum('gtin');
    }
}
