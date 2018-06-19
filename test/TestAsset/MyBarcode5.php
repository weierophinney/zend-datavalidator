<?php
/**
 * @see       https://github.com/zendframework/zend-datavalidator for the canonical source repository
 * @copyright Copyright (c) 2018 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   https://github.com/zendframework/zend-datavalidator/blob/master/LICENSE.md New BSD License
 */

declare(strict_types=1);

namespace ZendTest\DataValidator\TestAsset;

use Zend\DataValidator\Barcode\AbstractAdapter;

class MyBarcode5
{
    public function __construct()
    {
        $setLength = 'odd';
        $setCharacters = 128;
    }
}