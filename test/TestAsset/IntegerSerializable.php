<?php
/**
 * @see       https://github.com/zendframework/zend-datavalidator for the canonical source repository
 * @copyright Copyright (c) 2018 Zend Technologies USA Inc. (https://www.zend.com)
 * @license   https://github.com/zendframework/zend-datavalidator/blob/master/LICENSE.md New BSD License
 */

declare(strict_types=1);

namespace ZendTest\DataValidator\TestAsset;

use Countable;

class IntegerSerializable implements Countable
{
    /** @var int */
    private $count;

    public function __construct(int $count = 0)
    {
        $this->count = $count;
    }

    public function count()
    {
        return $this->count;
    }
}
