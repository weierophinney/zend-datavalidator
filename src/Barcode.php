<?php
/**
 * @see       https://github.com/zendframework/zend-datavalidator for the canonical source repository
 * @copyright Copyright (c) 2018 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   https://github.com/zendframework/zend-datavalidator/blob/master/LICENSE.md New BSD License
 */

declare(strict_types=1);

namespace Zend\DataValidator;

use Traversable;
use Zend\DataValidator\Barcode\AdapterInterface;

class Barcode extends AbstractValidator
{
    public const INVALID        = self::class . '::invalidValue';
    public const FAILED         = self::class . '::failedChecksum';
    public const INVALID_CHARS  = self::class . '::invalidChars';
    public const INVALID_LENGTH = self::class . '::invalidLength';

    /**
     * Validation failure message template definitions
     *
     * @var array
     */
    protected $messageTemplates = [
        self::FAILED         => "The input failed checksum validation",
        self::INVALID_CHARS  => "The input contains invalid characters",
        self::INVALID_LENGTH => "The input should have a length of %length% characters",
        self::INVALID        => "Invalid type given. String expected",
    ];

    /**
     * @var AdapterInterface
     */
    private $adapter;

    public function __construct(AdapterInterface $adapter)
    {
        $this->adapter = $adapter;

        $this->messageVariables = [
            'length' => null,
        ];
    }

    /**
     * Returns the set adapter
     *
     * @return AdapterInterface
     */
    public function getAdapter() : AdapterInterface
    {
        return $this->adapter;
    }

    /**
     * Validate the value is a correct barcode.
     *
     * @return ResultInterface
     */
    public function validate($value, $context = null) : ResultInterface
    {
        if (! is_string($value)) {
            return $this->createInvalidResult($value, [self::INVALID]);
        }

        $adapter = $this->getAdapter();
        if (! $adapter->hasValidLength($value)) {
            $this->messageVariables['length'] = $this->getAllowedLength($adapter);
            return $this->createInvalidResult($value, [self::INVALID_LENGTH]);
        }

        if (! $adapter->hasValidCharacters($value)) {
            return $this->createInvalidResult($value, [self::INVALID_CHARS]);
        }

        if ($adapter instanceof Barcode\ChecksummableInterface
            && $adapter->useChecksum($value)
            && ! $adapter->hasValidChecksum($value)
        ) {
            return $this->createInvalidResult($value, [self::FAILED]);
        }

        return Result::createValidResult($value);
    }

    /**
     * @return int|string If the adapter returns an array of values for the
     *     length, they will be imploded with a "/" character and returned
     *     as a string.
     */
    private function getAllowedLength(AdapterInterface $adapter)
    {
        $length = $adapter->getLength();
        if (! is_array($length)) {
            return $length;
        }

        return implode('/', $length);
    }
}
