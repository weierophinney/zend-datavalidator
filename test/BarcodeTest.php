<?php
declare(strict_types=1);
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2015 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   https://github.com/zendframework/zend-datavalidator/blob/master/LICENSE.md New BSD License
 */

namespace ZendTest\DataValidator;

use PHPUnit\Framework\TestCase;
use Zend\DataValidator\Barcode;
use Zend\DataValidator\Result;
use Zend\DataValidator\Exception\InvalidArgumentException;
use Zend\DataValidator\Barcode\AdapterInterface;
use Zend\DataValidator\Barcode\Ean13;
use Zend\DataValidator\Barcode\Issn;

/**
 * \Zend\Barcode
 *
 * @group      Zend_Validator
 */
class BarcodeTest extends TestCase
{

    private function validateResult(Result $result, $expectedResult)
    {
        $this->assertInstanceOf(Result::class, $result);
        $this->assertSame(
            $expectedResult,
            $result->isValid(),
            'Failed value: ' . var_export($result->getValue(), true)
        );
    }

    public function provideBarcodeConstructor()
    {
        return [
            'null' => [null, Ean13::class],
            'issn' => ['issn', Issn::class],
        ];
    }
    /**
     * @dataProvider provideBarcodeConstructor
     */
    public function testBarcodeConstructor($adapter, $expectedResult)
    {
        $barcode = new Barcode($adapter);
        $this->assertInstanceOf($expectedResult, $barcode->getAdapter());
    }

    public function testNoneExisting()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('not found');
        $barcode = new Barcode('\Zend\Validate\BarcodeTest\NonExistentClassName');
        $barcode->getAdapter();
    }

    public function testSetCustomAdapter()
    {
        $barcode = new Barcode($this->createMock(AdapterInterface::class));

        $this->assertInstanceOf(AdapterInterface::class, $barcode->getAdapter());
    }

    public function validationProvider()
    {
        return [
            'test-set-adapter-upca'  => ['upca', '065100004327', true],
            'test-set-adapter-ean13' => ['ean13', '0075678164125', true],
            // @ZF-4352
            'non-string-validation-upca-int'    => ['upca', 106510000.4327, false],
            'non-string-validation-upca-array'  => ['upca', ['065100004327'], false],
            'non-string-validation-ean13-int'   => ['ean13', 106510000.4327, false],
            'non-string-validation-ean13-array' => ['ean13', ['065100004327'], false],
        ];
    }

    /**
     * @dataProvider validationProvider
     */
    public function testValidateReturnsExpectedResults(
        $adapter,
        $input,
        bool $expectedResult
    ) {
        $validator = new Barcode($adapter);
        $this->validateResult($validator->validate($input), $expectedResult);
    }

    public function testInvalidChecksumAdapter()
    {
        require_once __DIR__ . "/_files/MyBarcode1.php";
        $barcode = new Barcode('MyBarcode1');
        $result = $barcode->validate('0000000');
        $this->assertSame(
            false,
            $result->isValid(),
            'Failed value: 0000000'
        );

        // $this->assertArrayHasKey('Zend\DataValidator\Barcode::barcodeFailed', $result->getMessages());
        $this->assertFalse($barcode->getAdapter()->hasValidChecksum('0000000'));
    }

    public function testInvalidCharAdapter()
    {
        require_once __DIR__ . "/_files/MyBarcode1.php";
        $barcode = new Barcode('MyBarcode1');
        $this->assertFalse($barcode->getAdapter()->hasValidCharacters(123));
    }

    public function testAscii128CharacterAdapter()
    {
        require_once __DIR__ . "/_files/MyBarcode2.php";
        $barcode = new Barcode('MyBarcode2');
        $this->assertTrue($barcode->getAdapter()->hasValidCharacters('1234QW!"'));
    }

    public function testInvalidLengthAdapter()
    {
        require_once __DIR__ . "/_files/MyBarcode2.php";
        $barcode = new Barcode('MyBarcode2');
        $this->assertFalse($barcode->getAdapter()->hasValidLength(123));
    }

    public function testArrayLengthAdapter()
    {
        require_once __DIR__ . "/_files/MyBarcode2.php";
        $barcode = new Barcode('MyBarcode2');
        $this->assertTrue($barcode->getAdapter()->hasValidLength('1'));
        $this->assertFalse($barcode->getAdapter()->hasValidLength('12'));
        $this->assertTrue($barcode->getAdapter()->hasValidLength('123'));
        $this->assertFalse($barcode->getAdapter()->hasValidLength('1234'));
    }

    public function testArrayLengthAdapter2()
    {
        require_once __DIR__ . "/_files/MyBarcode3.php";
        $barcode = new Barcode('MyBarcode3');
        $this->assertTrue($barcode->getAdapter()->hasValidLength('1'));
        $this->assertTrue($barcode->getAdapter()->hasValidLength('12'));
        $this->assertTrue($barcode->getAdapter()->hasValidLength('123'));
        $this->assertTrue($barcode->getAdapter()->hasValidLength('1234'));
    }

    public function testOddLengthAdapter()
    {
        require_once __DIR__ . "/_files/MyBarcode4.php";
        $barcode = new Barcode('MyBarcode4');
        $this->assertTrue($barcode->getAdapter()->hasValidLength('1'));
        $this->assertFalse($barcode->getAdapter()->hasValidLength('12'));
        $this->assertTrue($barcode->getAdapter()->hasValidLength('123'));
        $this->assertFalse($barcode->getAdapter()->hasValidLength('1234'));
    }

    public function testInvalidAdapter()
    {
        $barcode = new Barcode('Ean13');

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('does not implement');
        require_once __DIR__ . "/_files/MyBarcode5.php";
        $barcode->setAdapter('MyBarcode5');
    }

    public function testArrayConstructAdapter()
    {
        $barcode = new Barcode('Ean13', false);
        $this->assertInstanceOf(Ean13::class, $barcode->getAdapter());
        $this->assertFalse($barcode->useChecksum());
    }



    // public function testDefaultArrayConstructWithMissingAdapter()
    // {
    //     $barcode = new Barcode(['options' => 'unknown', 'checksum' => false]);
    //     $this->validateResult($barcode->validate('0075678164125'), true);
    // }
    //
    // public function testConfigConstructAdapter()
    // {
    //     $array = ['adapter' => 'Ean13', 'options' => 'unknown', 'useChecksum' => false];
    //     $config = new \Zend\Config\Config($array);
    //
    //     $barcode = new Barcode($config);
    //     $this->validateResult($barcode->validate('0075678164125'), true);
    // }

    public function testCODE25()
    {
        $barcode = new Barcode('code25');
        $this->validateResult($barcode->validate('0123456789101213'), true);
        $this->validateResult($barcode->validate('123'), true);
        $this->validateResult($barcode->validate('123a'), false);

        $barcode->useChecksum(true);
        $this->validateResult($barcode->validate('0123456789101214'), true);
        $this->validateResult($barcode->validate('0123456789101213'), false);
    }

    public function testCODE25INTERLEAVED()
    {
        $barcode = new Barcode('code25interleaved');
        $this->validateResult($barcode->validate('0123456789101213'), true);
        $this->validateResult($barcode->validate('123'), false);

        $barcode->useChecksum(true);
        $this->validateResult($barcode->validate('0123456789101214'), true);
        $this->validateResult($barcode->validate('0123456789101213'), false);
    }

    public function testCODE39()
    {
        $barcode = new Barcode('code39');
        $this->validateResult($barcode->validate('TEST93TEST93TEST93TEST93Y+'), true);
        $this->validateResult($barcode->validate('00075678164124'), true);
        $this->validateResult($barcode->validate('Test93Test93Test'), false);

        $barcode->useChecksum(true);
        $this->validateResult($barcode->validate('159AZH'), true);
        $this->validateResult($barcode->validate('159AZG'), false);
    }

    public function testCODE39EXT()
    {
        $barcode = new Barcode('code39ext');
        $this->validateResult($barcode->validate('TEST93TEST93TEST93TEST93Y+'), true);
        $this->validateResult($barcode->validate('00075678164124'), true);
        $this->validateResult($barcode->validate('Test93Test93Test'), true);

        // @TODO: CODE39 EXTENDED CHECKSUM VALIDATION MISSING
        // $barcode->useChecksum(true);
        // $this->validateResult($barcode->validate('159AZH'), true);
        // $this->validateResult($barcode->validate('159AZG'), false);
    }

    public function testCODE93()
    {
        $barcode = new Barcode('code93');
        $this->validateResult($barcode->validate('TEST93+'), true);
        $this->validateResult($barcode->validate('Test93+'), false);

        $barcode->useChecksum(true);
        $this->validateResult($barcode->validate('CODE 93E0'), true);
        $this->validateResult($barcode->validate('CODE 93E1'), false);
    }

    public function testCODE93EXT()
    {
        $barcode = new Barcode('code93ext');
        $this->validateResult($barcode->validate('TEST93+'), true);
        $this->validateResult($barcode->validate('Test93+'), true);

// @TODO: CODE93 EXTENDED CHECKSUM VALIDATION MISSING
//        $barcode->useChecksum(true);
//        $this->assertTrue($barcode->validate('CODE 93E0'));
//        $this->assertFalse($barcode->validate('CODE 93E1'));
    }

    public function testEAN2()
    {
        $barcode = new Barcode('ean2');
        $this->validateResult($barcode->validate('12'), true);
        $this->validateResult($barcode->validate('1'), false);
        $this->validateResult($barcode->validate('123'), false);
    }

    public function testEAN5()
    {
        $barcode = new Barcode('ean5');
        $this->validateResult($barcode->validate('12345'), true);
        $this->validateResult($barcode->validate('1234'), false);
        $this->validateResult($barcode->validate('123456'), false);
    }

    public function testEAN8()
    {
        $barcode = new Barcode('ean8');
        $this->validateResult($barcode->validate('12345670'), true);
        $this->validateResult($barcode->validate('123'), false);
        $this->validateResult($barcode->validate('1234567'), true);
        $this->validateResult($barcode->validate('12345671'), false);
    }

    public function testEAN12()
    {
        $barcode = new Barcode('ean12');
        $this->validateResult($barcode->validate('123456789012'), true);
        $this->validateResult($barcode->validate('123'), false);
        $this->validateResult($barcode->validate('123456789013'), false);
    }

    public function testEAN13()
    {
        $barcode = new Barcode('ean13');
        $this->validateResult($barcode->validate('1234567890128'), true);
        $this->validateResult($barcode->validate('123'), false);
        $this->validateResult($barcode->validate('1234567890127'), false);
    }

    public function testEAN14()
    {
        $barcode = new Barcode('ean14');
        $this->validateResult($barcode->validate('12345678901231'), true);
        $this->validateResult($barcode->validate('123'), false);
        $this->validateResult($barcode->validate('12345678901232'), false);
    }

    public function testEAN18()
    {
        $barcode = new Barcode('ean18');
        $this->validateResult($barcode->validate('123456789012345675'), true);
        $this->validateResult($barcode->validate('123'), false);
        $this->validateResult($barcode->validate('123456789012345676'), false);
    }

    public function testGTIN12()
    {
        $barcode = new Barcode('gtin12');
        $this->validateResult($barcode->validate('123456789012'), true);
        $this->validateResult($barcode->validate('123'), false);
        $this->validateResult($barcode->validate('123456789013'), false);
    }

    public function testGTIN13()
    {
        $barcode = new Barcode('gtin13');
        $this->validateResult($barcode->validate('1234567890128'), true);
        $this->validateResult($barcode->validate('123'), false);
        $this->validateResult($barcode->validate('1234567890127'), false);
    }

    public function testGTIN14()
    {
        $barcode = new Barcode('gtin14');
        $this->validateResult($barcode->validate('12345678901231'), true);
        $this->validateResult($barcode->validate('123'), false);
        $this->validateResult($barcode->validate('12345678901232'), false);
    }

    public function testIDENTCODE()
    {
        $barcode = new Barcode('identcode');
        $this->validateResult($barcode->validate('564000000050'), true);
        $this->validateResult($barcode->validate('123'), false);
        $this->validateResult($barcode->validate('0563102430313'), false);
        $this->validateResult($barcode->validate('564000000051'), false);
    }

    public function testINTELLIGENTMAIL()
    {
        $barcode = new Barcode('intelligentmail');
        $this->validateResult($barcode->validate('01234567094987654321'), true);
        $this->validateResult($barcode->validate('123'), false);
        $this->validateResult($barcode->validate('5555512371'), false);
    }

    public function testISSN()
    {
        $barcode = new Barcode('issn');
        $this->validateResult($barcode->validate('1144875X'), true);
        $this->validateResult($barcode->validate('123'), false);
        $this->validateResult($barcode->validate('1144874X'), false);

        $this->validateResult($barcode->validate('9771144875007'), true);
        $this->validateResult($barcode->validate('97711448750X7'), false);
    }

    public function testITF14()
    {
        $barcode = new Barcode('itf14');
        $this->validateResult($barcode->validate('00075678164125'), true);
        $this->validateResult($barcode->validate('123'), false);
        $this->validateResult($barcode->validate('00075678164124'), false);
    }

    public function testLEITCODE()
    {
        $barcode = new Barcode('leitcode');
        $this->validateResult($barcode->validate('21348075016401'), true);
        $this->validateResult($barcode->validate('123'), false);
        $this->validateResult($barcode->validate('021348075016401'), false);
        $this->validateResult($barcode->validate('21348075016402'), false);
    }

    public function testPLANET()
    {
        $barcode = new Barcode('planet');
        $this->validateResult($barcode->validate('401234567891'), true);
        $this->validateResult($barcode->validate('123'), false);
        $this->validateResult($barcode->validate('401234567892'), false);
    }

    public function testPOSTNET()
    {
        $barcode = new Barcode('postnet');
        $this->validateResult($barcode->validate('5555512372'), true);
        $this->validateResult($barcode->validate('123'), false);
        $this->validateResult($barcode->validate('5555512371'), false);
    }

    public function testROYALMAIL()
    {
        $barcode = new Barcode('royalmail');
        $this->validateResult($barcode->validate('SN34RD1AK'), true);
        $this->validateResult($barcode->validate('123'), false);
        $this->validateResult($barcode->validate('SN34RD1AW'), false);

        $this->validateResult($barcode->validate('012345W'), true);
        $this->validateResult($barcode->validate('06CIOUH'), true);
    }

    public function testSSCC()
    {
        $barcode = new Barcode('sscc');
        $this->validateResult($barcode->validate('123456789012345675'), true);
        $this->validateResult($barcode->validate('123'), false);
        $this->validateResult($barcode->validate('123456789012345676'), false);
    }

    public function testUPCA()
    {
        $barcode = new Barcode('upca');
        $this->validateResult($barcode->validate('123456789012'), true);
        $this->validateResult($barcode->validate('123'), false);
        $this->validateResult($barcode->validate('123456789013'), false);
    }

    public function testUPCE()
    {
        $barcode = new Barcode('upce');
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
        $barcode = new Barcode('ean8');
        $result = $barcode->validate('123');
        $this->validateResult($result, false);
        // $message = $barcode->getMessages();
        // $this->assertArrayHasKey('barcodeInvalidLength', $message);
        // $this->assertContains("length of 7/8 characters", $message['barcodeInvalidLength']);
    }

    /**
     * @group ZF-8673
     */
    public function testCODABAR()
    {
        $barcode = new Barcode('codabar');
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
        $barcode = new Barcode('issn');
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

        $barcode = new Barcode('code128');
        $this->validateResult($barcode->validate('ˆCODE128:Š'), true);
        $this->validateResult($barcode->validate('‡01231[Š'), true);

        $barcode->useChecksum(false);
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
        $barcode = new Barcode('ean13');
        $this->assertFalse($barcode->validate('3RH1131-1BB40')->isValid());
    }

    public function testEqualsMessageTemplates()
    {
        $validator = new Barcode('code25');
        $this->assertAttributeEquals(
            $validator->getMessageTemplates(),
            'messageTemplates',
            $validator
        );
    }

    public function testEqualsMessageVariables()
    {
        $validator = new Barcode('code25');
        $this->assertAttributeEquals(
            ['length' => null],
            'messageVariables',
            $validator
        );
    }
}
