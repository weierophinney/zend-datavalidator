<?php
/**
 * @see       https://github.com/zendframework/zend-datavalidator for the canonical source repository
 * @copyright Copyright (c) 2018 Zend Technologies USA Inc. (https://www.zend.com)
 * @license   https://github.com/zendframework/zend-datavalidator/blob/master/LICENSE.md New BSD License
 */

declare(strict_types=1);

namespace ZendTest\DataValidator\TestAsset;

class StringSerializable
{
    /** @var string */
    private $string;

    public function __construct(string $string = '')
    {
        $this->string = $string;
    }

    public function __toString()
    {
        return $this->string;
    }
}
