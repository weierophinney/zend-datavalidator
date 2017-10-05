<?php
/**
 * @see       https://github.com/zendframework/zend-datavalidator for the canonical source repository
 * @copyright Copyright (c) 2017 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   https://github.com/zendframework/zend-datavalidator/blob/master/LICENSE.md New BSD License
 */

namespace Zend\DataValidator\Barcode;

class Identcode extends AbstractAdapter
{
    /**
     * Allowed barcode lengths
     * @var int
     */
    protected $length = 12;

    /**
     * Allowed barcode characters
     * @var string
     */
    protected $characters = '0123456789';

    /**
     * Checksum function
     * @var string
     */
    protected $checksum = 'identcode';

    /**
     * Constructor for this barcode adapter
     */
    public function __construct()
    {
        $this->setLength(12);
        $this->setCharacters('0123456789');
        $this->setChecksum('identcode');
    }
}
