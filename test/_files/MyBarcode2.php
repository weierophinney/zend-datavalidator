<?php
/**
 * @see       https://github.com/zendframework/zend-datavalidator for the canonical source repository
 * @copyright Copyright (c) 2017 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   https://github.com/zendframework/zend-datavalidator/blob/master/LICENSE.md New BSD License
 */

namespace ZendTest\DataValidator\Barcode;

use Zend\DataValidator\Barcode\AbstractAdapter;

class MyBarcode2 extends AbstractAdapter
{
    public function __construct()
    {
        $this->setLength([1, 3, 6]);
        $this->setCharacters(128);
        $this->setChecksum('_mod10');
    }
}
