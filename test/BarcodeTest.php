<?php
/**
 * @see       https://github.com/zendframework/zend-datavalidator for the canonical source repository
 * @copyright Copyright (c) 2017 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   https://github.com/zendframework/zend-datavalidator/blob/master/LICENSE.md New BSD License
 */

declare(strict_types=1);

namespace ZendTest\DataValidator;

use PHPUnit\Framework\TestCase;
use Zend\DataValidator\Barcode;
use Zend\DataValidator\Barcode\AdapterInterface;
use Zend\DataValidator\Exception\InvalidArgumentException;
use Zend\DataValidator\Result;
use ZendTest\DataValidator\TestAsset\MyBarcode1;
use ZendTest\DataValidator\TestAsset\MyBarcode2;
use ZendTest\DataValidator\TestAsset\MyBarcode3;
use ZendTest\DataValidator\TestAsset\MyBarcode4;

/**
 * Barcode Test
 */
class BarcodeTest extends TestCase
{
    private function validateResult(Result $result, $expectedResult)
    {
        $this->assertSame(
            $expectedResult,
            $result->isValid(),
            sprintf(
                'Expected validation result "%s" was not received; value validated: %s',
                var_export($expectedResult, true),
                var_export($result->getValue(), true)
            )
        );
    }

    public function testBarcodeConstructor()
    {
        $barcode = new Barcode($this->createMock(AdapterInterface::class));
        $this->assertInstanceOf(AdapterInterface::class, $barcode->getAdapter());
    }

    public function validationProvider()
    {
        return [
            'test-set-adapter-upca'  => [ new Barcode\Upca(), '065100004327', true],
            'test-set-adapter-ean13' => [ new Barcode\Ean13(), '0075678164125', true],
            // @ZF-4352
            'non-string-validation-upca-int'    => [ new Barcode\Upca(), 106510000.4327, false],
            'non-string-validation-upca-array'  => [ new Barcode\Upca(), ['065100004327'], false],
            'non-string-validation-ean13-int'   => [ new Barcode\Ean13(), 106510000.4327, false],
            'non-string-validation-ean13-array' => [ new Barcode\Ean13(), ['065100004327'], false],
        ];
    }

    /**
     * @dataProvider validationProvider
     */
    public function testValidateReturnsExpectedResults(
        AdapterInterface $adapter,
        $input,
        bool $expectedResult
    ) {
        $validator = new Barcode($adapter);
        $this->validateResult($validator->validate($input), $expectedResult);
    }

    public function testInvalidCharAdapter()
    {
        $barcode = new Barcode(new MyBarcode1());
        $this->assertFalse($barcode->getAdapter()->hasValidCharacters('123'));
    }

    public function testAscii128CharacterAdapter()
    {
        $barcode = new Barcode(new MyBarcode2());
        $this->assertTrue($barcode->getAdapter()->hasValidCharacters('1234QW!"'));
    }

    public function testInvalidLengthAdapter()
    {
        $barcode = new Barcode(new MyBarcode2());
        $this->assertFalse($barcode->getAdapter()->hasValidLength('1234'));
    }

    public function testArrayLengthAdapter()
    {
        $barcode = new Barcode(new MyBarcode2());
        $this->assertTrue($barcode->getAdapter()->hasValidLength('1'));
        $this->assertFalse($barcode->getAdapter()->hasValidLength('12'));
        $this->assertTrue($barcode->getAdapter()->hasValidLength('123'));
        $this->assertFalse($barcode->getAdapter()->hasValidLength('1234'));
    }

    public function testArrayLengthAdapter2()
    {
        $barcode = new Barcode(new MyBarcode3());
        $this->assertTrue($barcode->getAdapter()->hasValidLength('1'));
        $this->assertTrue($barcode->getAdapter()->hasValidLength('12'));
        $this->assertTrue($barcode->getAdapter()->hasValidLength('123'));
        $this->assertTrue($barcode->getAdapter()->hasValidLength('1234'));
    }

    public function testOddLengthAdapter()
    {
        $barcode = new Barcode(new MyBarcode4());
        $this->assertTrue($barcode->getAdapter()->hasValidLength('1'));
        $this->assertFalse($barcode->getAdapter()->hasValidLength('12'));
        $this->assertTrue($barcode->getAdapter()->hasValidLength('123'));
        $this->assertFalse($barcode->getAdapter()->hasValidLength('1234'));
    }

    public function testCODE25()
    {
        $barcode = new Barcode(new Barcode\Code25());
        $this->validateResult($barcode->validate('0123456789101213'), true);
        $this->validateResult($barcode->validate('123'), true);
        $this->validateResult($barcode->validate('123a'), false);

        // using checksum:
        $barcode = new Barcode(new Barcode\Code25(true));
        $this->validateResult($barcode->validate('0123456789101214'), true);
        $this->validateResult($barcode->validate('0123456789101213'), false);
    }

    public function testCODE25INTERLEAVED()
    {
        $barcode = new Barcode(new Barcode\Code25interleaved());
        $this->validateResult($barcode->validate('0123456789101213'), true);
        $this->validateResult($barcode->validate('123'), false);

        // using checksum:
        $barcode = new Barcode(new Barcode\Code25interleaved(true));
        $this->validateResult($barcode->validate('0123456789101214'), true);
        $this->validateResult($barcode->validate('0123456789101213'), false);
    }

    public function testCODE39()
    {
        $barcode = new Barcode(new Barcode\Code39());
        $this->validateResult($barcode->validate('TEST93TEST93TEST93TEST93Y+'), true);
        $this->validateResult($barcode->validate('00075678164124'), true);
        $this->validateResult($barcode->validate('Test93Test93Test'), false);

        // using checksum:
        $barcode = new Barcode(new Barcode\Code39(true));
        $this->validateResult($barcode->validate('159AZH'), true);
        $this->validateResult($barcode->validate('159AZG'), false);
    }

    public function testCODE39EXT()
    {
        $barcode = new Barcode(new Barcode\Code39ext());
        $this->validateResult($barcode->validate('TEST93TEST93TEST93TEST93Y+'), true);
        $this->validateResult($barcode->validate('00075678164124'), true);
        $this->validateResult($barcode->validate('Test93Test93Test'), true);
    }

    public function testCODE93()
    {
        $barcode = new Barcode(new Barcode\Code93());
        $this->validateResult($barcode->validate('TEST93+'), true);
        $this->validateResult($barcode->validate('Test93+'), false);

        // using checksum:
        $barcode = new Barcode(new Barcode\Code93(true));
        $this->validateResult($barcode->validate('CODE 93E0'), true);
        $this->validateResult($barcode->validate('CODE 93E1'), false);
    }

    public function testCODE93EXT()
    {
        $barcode = new Barcode(new Barcode\Code93ext());
        $this->validateResult($barcode->validate('TEST93+'), true);
        $this->validateResult($barcode->validate('Test93+'), true);
    }

    public function testEAN2()
    {
        $barcode = new Barcode(new Barcode\Ean2());
        $this->validateResult($barcode->validate('12'), true);
        $this->validateResult($barcode->validate('1'), false);
        $this->validateResult($barcode->validate('123'), false);
    }

    public function testEAN5()
    {
        $barcode = new Barcode(new Barcode\Ean5());
        $this->validateResult($barcode->validate('12345'), true);
        $this->validateResult($barcode->validate('1234'), false);
        $this->validateResult($barcode->validate('123456'), false);
    }

    public function testEAN8()
    {
        $barcode = new Barcode(new Barcode\Ean8());
        $this->validateResult($barcode->validate('12345670'), true);
        $this->validateResult($barcode->validate('123'), false);
        $this->validateResult($barcode->validate('1234567'), true);
        $this->validateResult($barcode->validate('12345671'), false);
    }

    public function testEAN12()
    {
        $barcode = new Barcode(new Barcode\Ean12());
        $this->validateResult($barcode->validate('123456789012'), true);
        $this->validateResult($barcode->validate('123'), false);
        $this->validateResult($barcode->validate('123456789013'), false);
    }

    public function testEAN13()
    {
        $barcode = new Barcode(new Barcode\Ean13());
        $this->validateResult($barcode->validate('1234567890128'), true);
        $this->validateResult($barcode->validate('123'), false);
        $this->validateResult($barcode->validate('1234567890127'), false);
    }

    public function testEAN14()
    {
        $barcode = new Barcode(new Barcode\Ean14());
        $this->validateResult($barcode->validate('12345678901231'), true);
        $this->validateResult($barcode->validate('123'), false);
        $this->validateResult($barcode->validate('12345678901232'), false);
    }

    public function testEAN18()
    {
        $barcode = new Barcode(new Barcode\Ean18());
        $this->validateResult($barcode->validate('123456789012345675'), true);
        $this->validateResult($barcode->validate('123'), false);
        $this->validateResult($barcode->validate('123456789012345676'), false);
    }

    public function testGTIN12()
    {
        $barcode = new Barcode(new Barcode\Gtin12());
        $this->validateResult($barcode->validate('123456789012'), true);
        $this->validateResult($barcode->validate('123'), false);
        $this->validateResult($barcode->validate('123456789013'), false);
    }

    public function testGTIN13()
    {
        $barcode = new Barcode(new Barcode\Gtin13());
        $this->validateResult($barcode->validate('1234567890128'), true);
        $this->validateResult($barcode->validate('123'), false);
        $this->validateResult($barcode->validate('1234567890127'), false);
    }

    public function testGTIN14()
    {
        $barcode = new Barcode(new Barcode\Gtin14());
        $this->validateResult($barcode->validate('12345678901231'), true);
        $this->validateResult($barcode->validate('123'), false);
        $this->validateResult($barcode->validate('12345678901232'), false);
    }

    public function testIDENTCODE()
    {
        $barcode = new Barcode(new Barcode\Identcode());
        $this->validateResult($barcode->validate('564000000050'), true);
        $this->validateResult($barcode->validate('123'), false);
        $this->validateResult($barcode->validate('0563102430313'), false);
        $this->validateResult($barcode->validate('564000000051'), false);
    }

    public function testINTELLIGENTMAIL()
    {
        $barcode = new Barcode(new Barcode\Intelligentmail());
        $this->validateResult($barcode->validate('01234567094987654321'), true);
        $this->validateResult($barcode->validate('123'), false);
        $this->validateResult($barcode->validate('5555512371'), false);
    }

    public function testISSN()
    {
        $barcode = new Barcode(new Barcode\Issn());
        $this->validateResult($barcode->validate('1144875X'), true);
        $this->validateResult($barcode->validate('123'), false);
        $this->validateResult($barcode->validate('1144874X'), false);

        $this->validateResult($barcode->validate('9771144875007'), true);
        $this->validateResult($barcode->validate('97711448750X7'), false);
    }

    public function testITF14()
    {
        $barcode = new Barcode(new Barcode\Itf14());
        $this->validateResult($barcode->validate('00075678164125'), true);
        $this->validateResult($barcode->validate('123'), false);
        $this->validateResult($barcode->validate('00075678164124'), false);
    }

    public function testLEITCODE()
    {
        $barcode = new Barcode(new Barcode\Leitcode());
        $this->validateResult($barcode->validate('21348075016401'), true);
        $this->validateResult($barcode->validate('123'), false);
        $this->validateResult($barcode->validate('021348075016401'), false);
        $this->validateResult($barcode->validate('21348075016402'), false);
    }

    public function testPLANET()
    {
        $barcode = new Barcode(new Barcode\Planet());
        $this->validateResult($barcode->validate('401234567891'), true);
        $this->validateResult($barcode->validate('123'), false);
        $this->validateResult($barcode->validate('401234567892'), false);
    }

    public function testPOSTNET()
    {
        $barcode = new Barcode(new Barcode\Postnet());
        $this->validateResult($barcode->validate('5555512372'), true);
        $this->validateResult($barcode->validate('123'), false);
        $this->validateResult($barcode->validate('5555512371'), false);
    }

    public function testROYALMAIL()
    {
        $barcode = new Barcode(new Barcode\Royalmail());
        $this->validateResult($barcode->validate('SN34RD1AK'), true);
        $this->validateResult($barcode->validate('123'), false);
        $this->validateResult($barcode->validate('SN34RD1AW'), false);

        $this->validateResult($barcode->validate('012345W'), true);
        $this->validateResult($barcode->validate('06CIOUH'), true);
    }

    public function testSSCC()
    {
        $barcode = new Barcode(new Barcode\Sscc());
        $this->validateResult($barcode->validate('123456789012345675'), true);
        $this->validateResult($barcode->validate('123'), false);
        $this->validateResult($barcode->validate('123456789012345676'), false);
    }

    public function testUPCA()
    {
        $barcode = new Barcode(new Barcode\Upca());
        $this->validateResult($barcode->validate('123456789012'), true);
        $this->validateResult($barcode->validate('123'), false);
        $this->validateResult($barcode->validate('123456789013'), false);
    }

    public function testUPCE()
    {
        $barcode = new Barcode(new Barcode\Upce());
        $this->validateResult($barcode->validate('02345673'), true);
        $this->validateResult($barcode->validate('02345672'), false);
        $this->validateResult($barcode->validate('123'), false);
        $this->validateResult($barcode->validate('123456'), true);
        $this->validateResult($barcode->validate('0234567'), true);
    }

    /**
     * @group ZF-10116
     */
    public function testArrayLengthMessage()
    {
        $barcode = new Barcode(new Barcode\Ean8());
        $result = $barcode->validate('123');
        $this->validateResult($result, false);
        $messages = $result->getMessages();
        $this->assertCount(1, $messages);
        $message = $messages[0];
        $this->assertEquals(Barcode::INVALID_LENGTH, $message->getCode());
        $this->assertContains($barcode->getMessageTemplates()[Barcode::INVALID_LENGTH], $message->getTemplate());
        $this->assertEquals(['length' => '7/8'], $message->getVariables());
    }

    /**
     * @group ZF-8673
     */
    public function testCODABAR()
    {
        $barcode = new Barcode(new Barcode\Codabar());
        $this->validateResult($barcode->validate('123456789'), true);
        $this->validateResult($barcode->validate('A123A'), true);
        $this->validateResult($barcode->validate('A123C'), true);
        $this->validateResult($barcode->validate('A123E'), false);
        $this->validateResult($barcode->validate('A1A23C'), false);
        $this->validateResult($barcode->validate('T123*'), true);
        $this->validateResult($barcode->validate('*123A'), false);
    }

    /**
     * @group ZF-11532
     */
    public function testIssnWithMod0()
    {
        $barcode = new Barcode(new Barcode\Issn());
        $this->validateResult($barcode->validate('18710360'), true);
    }

    /**
     * @group ZF-8674
     */
    public function testCODE128()
    {
        if (! extension_loaded('iconv')) {
            $this->markTestSkipped('Missing ext/iconv');
        }

        // Using checksum:
        $barcode = new Barcode(new Barcode\Code128());
        $this->validateResult($barcode->validate('ˆCODE128:Š'), true);
        $this->validateResult($barcode->validate('‡01231[Š'), true);

        // No checksum:
        $barcode = new Barcode(new Barcode\Code128(false));
        $this->validateResult($barcode->validate('012345'), true);
        $this->validateResult($barcode->validate('ABCDEF'), true);
        $this->validateResult($barcode->validate('01234Ê'), false);
    }

    /**
     * Test if EAN-13 contains only numeric characters
     *
     * @group ZF-3297
     */
    public function testEan13ContainsOnlyNumeric()
    {
        $barcode = new Barcode(new Barcode\Ean13());
        $this->assertFalse($barcode->validate('3RH1131-1BB40')->isValid());
    }

    public function testEqualsMessageTemplates()
    {
        $validator = new Barcode(new Barcode\Code25());
        $this->assertAttributeEquals(
            $validator->getMessageTemplates(),
            'messageTemplates',
            $validator
        );
    }

    public function testEqualsMessageVariables()
    {
        $validator = new Barcode(new Barcode\Code25());
        $this->assertAttributeEquals(
            ['length' => null],
            'messageVariables',
            $validator
        );
    }
}
