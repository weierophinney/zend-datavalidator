<?php
/**
 * @see       https://github.com/zendframework/zend-datavalidator for the canonical source repository
 * @copyright Copyright (c) 2018 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   https://github.com/zendframework/zend-datavalidator/blob/master/LICENSE.md New BSD License
 */

declare(strict_types=1);

namespace ZendTest\DataValidator;

use DateTime;
use DateTimeImmutable;
use PHPUnit\Framework\TestCase;
use stdClass;
use Zend\DataValidator\Date;

class DateTest extends TestCase
{

    public function datesDataProvider()
    {
        return [
            // date                     format             isValid
            ['2007-01-01',              null,              true],
            ['2007-02-28',              null,              true],
            ['2007-02-29',              null,              false],
            ['2008-02-29',              null,              true],
            ['2007-02-30',              null,              false],
            ['2007-02-99',              null,              false],
            ['2007-02-99',              'Y-m-d',           false],
            ['9999-99-99',              null,              false],
            ['9999-99-99',              'Y-m-d',           false],
            ['Jan 1 2007',              null,              false],
            ['Jan 1 2007',              'M j Y',           true],
            ['asdasda',                 null,              false],
            ['sdgsdg',                  null,              false],
            ['2007-01-01something',     null,              false],
            ['something2007-01-01',     null,              false],
            ['10.01.2008',              'd.m.Y',           true],
            ['01 2010',                 'm Y',             true],
            ['2008/10/22',              'd/m/Y',           false],
            ['22/10/08',                'd/m/y',           true],
            ['22/10',                   'd/m/Y',           false],
            ['01 2010',                 'm Y',              true],
            ['2008/10/22',              'd/m/Y',           false],
            ['22/10/08',                'd/m/Y',            true],
            ['22/10',                   'd/m/Y',           false],

            // time
            ['2007-01-01T12:02:55Z',    DateTime::ISO8601, true],
            ['12:02:55',                'H:i:s',           true],
            ['25:02:55',                'H:i:s',           false],

            // int
            [0,                         null,              true],
            [1340677235,                null,              true],

            // 32bit version of php will convert this to double
            [999999999999,              null,              true],

            // double
            [12.12,                     null,              false],

            // array
            [['2012', '06', '25'],      null,              true],

            // 0012-06-25 is a valid date, if you want 2012, use 'y' instead of 'Y'
            [['12', '06', '25'],        null,              true],
            [['2012', '06', '33'],      null,              false],
            [[1 => 1],                  null,              false],

            // DateTime
            [new DateTime(),            null,              true],
            [new DateTimeImmutable(),   null ,              true],

            // invalid obj
            [new stdClass(),            null,              false],
        ];
    }

    /**
     * Ensures that the validator follows expected behavior
     *
     * @dataProvider datesDataProvider
     */
    public function testBasic($input, $format, $expectedResult)
    {
        $validator = new Date($format);
        $result = $validator->validate($input);
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

    public function testEqualsMessageVariables()
    {
        $validator = new Date('Y-m-d H:i:s');
        $this->assertAttributeEquals(['format' => 'Y-m-d H:i:s'], 'messageVariables', $validator);
    }

    public function testConstructorWithFormatParameter()
    {
        $format = 'd/m/Y';
        $validator = new Date($format);

        $this->assertEquals($format, $validator->getFormat());
    }
}
