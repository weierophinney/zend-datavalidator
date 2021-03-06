<?php
/**
 * @see       https://github.com/zendframework/zend-datavalidator for the canonical source repository
 * @copyright Copyright (c) 2018 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   https://github.com/zendframework/zend-datavalidator/blob/master/LICENSE.md New BSD License
 */

declare(strict_types=1);

namespace Zend\DataValidator\Barcode;

class Intelligentmail extends AbstractAdapter
{
    public function __construct()
    {
        $this->setLength([20, 25, 29, 31]);
        $this->setCharacters('0123456789');
    }
}
