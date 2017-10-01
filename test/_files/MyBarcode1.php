<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2015 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   https://github.com/zendframework/zend-datavalidator/blob/master/LICENSE.md New BSD License
 */

namespace Zend\DataValidator\Barcode;

class MyBarcode1 extends AbstractAdapter
{
    public function __construct()
    {
        $this->setLength(-1);
        $this->setCharacters(0);
        $this->setChecksum('invalid');
    }
}
