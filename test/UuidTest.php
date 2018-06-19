<?php
/**
 * @see       https://github.com/zendframework/zend-datavalidator for the canonical source repository
 * @copyright Copyright (c) 2017 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   https://github.com/zendframework/zend-datavalidator/blob/master/LICENSE.md New BSD License
 */

declare(strict_types=1);

namespace ZendTest\DataValidator;

use PHPUnit\Framework\TestCase;
use stdClass;
use Zend\DataValidator\Result;
use Zend\DataValidator\Uuid;

/**
 * Class UuidTest.
 *
 * Uuid test cases based on https://github.com/beberlei/assert/blob/master/tests/Assert/Tests/AssertTest.php
 */
final class UuidTest extends TestCase
{
    /**
     * @var Uuid
     */
    protected $validator;

    public function setUp()
    {
        $this->validator = new Uuid();
    }

    /**
     * @param $uuid
     * @dataProvider validUuidProvider
     */
    public function testValidUuid($input)
    {
        $result = $this->validator->validate($input);
        $this->assertInstanceOf(Result::class, $result);
        $this->assertSame(true, $result->isValid());
    }

    /**
     * @param $uuid
     * @dataProvider invalidUuidProvider
     */
    public function testInvalidUuid($input, $expectedMessageKey)
    {
        $result = $this->validator->validate($input);
        $this->assertInstanceOf(Result::class, $result);
        $this->assertSame(false, $result->isValid());

        $messages = $result->getMessages();
        $this->assertCount(1, $messages);

        foreach ($messages as $message) {
            $this->assertEquals($expectedMessageKey, $message->getCode());
            $this->assertEquals($this->validator->getMessageTemplates()[$expectedMessageKey], $message->getTemplate());
        }
    }

    /**
     * @return array
     */
    public function validUuidProvider()
    {
        return [
            'zero-fill' => ['00000000-0000-0000-0000-000000000000'],
            'version-1' => ['ff6f8cb0-c57d-11e1-9b21-0800200c9a66'],
            'version-2' => ['ff6f8cb0-c57d-21e1-9b21-0800200c9a66'],
            'version-3' => ['ff6f8cb0-c57d-31e1-9b21-0800200c9a66'],
            'version-4' => ['ff6f8cb0-c57d-41e1-9b21-0800200c9a66'],
            'version-5' => ['ff6f8cb0-c57d-51e1-9b21-0800200c9a66'],
            'uppercase' => ['FF6F8CB0-C57D-11E1-9B21-0800200C9A66'],
        ];
    }

    /**
     * @return array
     */
    public function invalidUuidProvider()
    {
        return [
            'invalid-characters' => ['zf6f8cb0-c57d-11e1-9b21-0800200c9a66', Uuid::INVALID],
            'missing-separators' => ['af6f8cb0c57d11e19b210800200c9a66', Uuid::INVALID],
            'invalid-segment-2'  => ['ff6f8cb0-c57da-51e1-9b21-0800200c9a66', Uuid::INVALID],
            'invalid-segment-1'  => ['af6f8cb-c57d-11e1-9b21-0800200c9a66', Uuid::INVALID],
            'invalid-segement-5' => ['3f6f8cb0-c57d-11e1-9b21-0800200c9a6', Uuid::INVALID],
            'truncated'          => ['3f6f8cb0', Uuid::INVALID],
            'empty-string'       => ['', Uuid::INVALID],
            'all-numeric'        => [123, Uuid::NOT_STRING],
            'object'             => [new stdClass(), Uuid::NOT_STRING],
        ];
    }
}
