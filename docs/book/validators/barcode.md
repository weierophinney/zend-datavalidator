# Barcode Validator

`Zend\DataValidator\Barcode` allows you to check if a given value is a valid
barcode.

## Basic usage

To validate if a given string is a barcode you must know its type. See the
following example for an [EAN13](#ean13) barcode:

```php
use Zend\DataValidator\Barcode;

$validator = new Barcode(new Barcode\Ean13());
$result = $validator->validate($input);
if ($result->isValid()) {
    // input appears to be valid
} else {
    // input is invalid
}
```

## Supported barcodes

`Zend\DataValidator\Barcode` supports multiple barcode standards and can be extended
with proprietary barcode implementations. The following barcode standards are
supported:

### CODABAR

Also known as Code-a-bar.

This barcode has no length limitation. It supports only digits and 6 special
chars. Codabar is a self-checking barcode. This standard is very old. Common use
cases are within airbills or photo labs where multi-part forms are used with
dot-matrix printers.

### CODE128

CODE128 is a high density barcode.

This barcode has no length limitation. It supports the first 128 ASCII
characters. When used with printing characters, it has a checksum which is
calculated modulo 103. This standard is used worldwide as it supports upper and
lowercase characters.

The constructor accepts two optional arguments:

- A boolean `$useChecksum` value: used to enable or disable checksum validation.
  The value is `true` by default.

- A `Zend\Stdlib\StringWrapper\StringWrapperInterface` instance. If none is
  provided, the default returned by `Zend\Stdlib\StringUtils::getWrapper('UTF-8')`
  will be used. The instance is used to allow calculating multibyte string
  lengths, as well as to extract characters from the string.

### CODE25

Often called "two of five" or "Code25 Industrial".

This barcode has no length limitation. It supports only digits, and the last
digit can be an optional checksum which is calculated with modulo 10. This
standard is very old and nowadays not often used. Common use cases are within
the industry.

The constructor accepts one optional argument:

- A boolean `$useChecksum` value: used to enable or disable checksum validation.
  The value is `false` by default.

### CODE25INTERLEAVED

Often called "Code 2 of 5 Interleaved".

This standard is a variant of CODE25. It has no length limitation, but it must
contain an even amount of characters. It supports only digits, and the last
digit can be an optional checksum which is calculated with modulo 10. It is used
worldwide, and common on the market.

The constructor accepts one optional argument:

- A boolean `$useChecksum` value: used to enable or disable checksum validation.
  The value is `false` by default.

### CODE39

CODE39 is one of the oldest available codes.

This barcode has a variable length. It supports digits, upper cased alphabetical
characters, and 7 special characters including whitespace, the period character,
and the dollar sign. It can have an optional checksum which is calculated with
modulo 43. This standard is used worldwide and common within the industry.

The constructor accepts one optional argument:

- A boolean `$useChecksum` value: used to enable or disable checksum validation.
  The value is `false` by default.

### CODE39EXT

CODE39EXT is an extension of [CODE39](#code39).

This barcode has the same properties as CODE39. Additionally it allows the usage
of all 128 ASCII characters. This standard is used worldwide and common within
the industry.

The constructor accepts one optional argument:

- A boolean `$useChecksum` value: used to enable or disable checksum validation.
  The value is `false` by default.

### CODE93

CODE93 is the successor of [CODE39](#code39).

This barcode has a variable length. It supports digits, alphabetical characters,
and 7 special characters. It has an optional checksum which is calculated with
modulo 47 and contains 2 characters. This standard produces a denser code than
[CODE39](#code39) and is more secure.

The constructor accepts one optional argument:

- A boolean `$useChecksum` value: used to enable or disable checksum validation.
  The value is `false` by default.

### CODE93EXT

CODE93EXT is an extension of [CODE93](#code93).

This barcode has the same properties as [CODE93](#code93). Additionally it
allows the usage of all 128 ASCII characters. This standard is used worldwide
and common within the industry.

The constructor accepts one optional argument:

- A boolean `$useChecksum` value: used to enable or disable checksum validation.
  The value is `false` by default.

### EAN2

EAN is the shortcut for "European Article Number".

This barcode consists of exactly 2 characters. It supports only digits, and does
not have a checksum. This standard is mainly used as an addition to EAN13 (ISBN)
when printed on books.

### EAN5

EAN is the shortcut for "European Article Number".

This barcode consists of exactly 5 characters. It supports only digits, and does
not have a checksum. This standard is mainly used as an addition to EAN13 (ISBN)
when printed on books.

### EAN8

EAN is the shortcut for "European Article Number".

This barcode consists of either 7 or 8 characters, and supports only digits.
When it has a length of 8 characters, the final character is a checksum. This
standard is used worldwide, but has a very limited range. It can be found on
small articles where a longer barcode could not be printed.

### EAN12

EAN is the shortcut for "European Article Number".

This barcode must have a length of 12 characters. It supports only digits, and
the last digit is always a checksum which is calculated with modulo 10. This
standard is used within the USA, and is common on the market. It has been
superseded by [EAN13](#ean13).

The constructor accepts one optional argument:

- A boolean `$useChecksum` value: used to enable or disable checksum validation.
  The value is `true` by default.

### EAN13

EAN is the shortcut for "European Article Number".

This barcode must have a length of 13 characters. It supports only digits, and
the last digit is always a checksum which is calculated with modulo 10. This
standard is used worldwide and is common on the market.

The constructor accepts one optional argument:

- A boolean `$useChecksum` value: used to enable or disable checksum validation.
  The value is `true` by default.

### EAN14

EAN is the shortcut for "European Article Number".

This barcode must have a length of 14 characters. It supports only digits, and
the last digit is always a checksum which is calculated with modulo 10. This
standard is used worldwide and is common on the market. It is the successor for
[EAN13](#ean13).

The constructor accepts one optional argument:

- A boolean `$useChecksum` value: used to enable or disable checksum validation.
  The value is `true` by default.

### EAN18

EAN is the shortcut for "European Article Number".

This barcode must have a length of 18 characters. It support only digits. The
last digit is always a checksum digit which is calculated with modulo 10. This
code is often used for the identification of shipping containers.

The constructor accepts one optional argument:

- A boolean `$useChecksum` value: used to enable or disable checksum validation.
  The value is `true` by default.

### GTIN12

GTIN is the shortcut for "Global Trade Item Number".

This barcode uses the same standard as [EAN12](#ean12) and is its successor.
It's commonly used within the USA.

The constructor accepts one optional argument:

- A boolean `$useChecksum` value: used to enable or disable checksum validation.
  The value is `true` by default.

### GTIN13

GTIN is the shortcut for "Global Trade Item Number".

This barcode uses the same standard as [EAN13](#ean13) and is its successor. It
is used worldwide by industry.

The constructor accepts one optional argument:

- A boolean `$useChecksum` value: used to enable or disable checksum validation.
  The value is `true` by default.

### GTIN14

GTIN is the shortcut for "Global Trade Item Number".

This barcode uses the same standard as [EAN14](#ean14) and is its successor. It
is used worldwide and common on the market.

The constructor accepts one optional argument:

- A boolean `$useChecksum` value: used to enable or disable checksum validation.
  The value is `true` by default.

### IDENTCODE

Identcode is used by Deutsche Post and DHL. It's a specialized implementation
of [CODE25](#code25).

This barcode must have a length of 12 characters. It supports only digits, and
the last digit is always a checksum which is calculated with modulo 10. This
standard is mainly used by the companies DP and DHL.

The constructor accepts one optional argument:

- A boolean `$useChecksum` value: used to enable or disable checksum validation.
  The value is `true` by default.

### INTELLIGENTMAIL

Intelligent Mail is a postal barcode.

This barcode can have a length of 20, 25, 29, or 31 characters. It supports only
digits, and contains no checksum. This standard is the successor of
[PLANET](#planet) and [POSTNET](#postnet). It is mainly used by the United
States Postal Services.

### ISSN

ISSN is the abbreviation for International Standard Serial Number.

This barcode can have a length of 8 or 13 characters. It supports only digits,
and the last digit must be a checksum digit which is calculated with modulo 11.
It is used worldwide for printed publications.

The constructor accepts one optional argument:

- A boolean `$useChecksum` value: used to enable or disable checksum validation.
  The value is `true` by default.

### ITF14

ITF14 is the GS1 implementation of an [Interleaved Two of
Five](#code25interleaved) bar code.

This barcode is a special variant of [Interleaved 2 of 5](#code25interleaved).
It must have a length of 14 characters and is based on [GTIN14](#gtin14). It
supports only digits, and the last digit must be a checksum digit which is
calculated with modulo 10. It is used worldwide, and is common within the
market.

The constructor accepts one optional argument:

- A boolean `$useChecksum` value: used to enable or disable checksum validation.
  The value is `true` by default.

### LEITCODE

Leitcode is used by Deutsche Post and DHL. It's a specialized implementation of
[Code25](#code25).

This barcode must have a length of 14 characters. It supports only digits, and
the last digit is always a checksum which is calculated with modulo 10. This
standard is mainly used by the companies DP and DHL.

The constructor accepts one optional argument:

- A boolean `$useChecksum` value: used to enable or disable checksum validation.
  The value is `true` by default.

### PLANET

Planet is the abbreviation for Postal Alpha Numeric Encoding Technique.

This barcode can have a length of 12 or 14 characters. It supports only digits,
and the last digit is always a checksum. This standard is mainly used by the
United States Postal Services.

The constructor accepts one optional argument:

- A boolean `$useChecksum` value: used to enable or disable checksum validation.
  The value is `true` by default.

### POSTNET

Postnet is used by the US Postal Service.

This barcode can have a length of 6, 7, 10 or 12 characters. It supports only
digits, and the last digit is always a checksum. This standard is mainly used by
the United States Postal Services.

The constructor accepts one optional argument:

- A boolean `$useChecksum` value: used to enable or disable checksum validation.
  The value is `true` by default.

### ROYALMAIL

Royalmail is used by Royal Mail.

This barcode has no defined length. It supports digits, uppercase letters, and
the last digit is always a checksum. This standard is mainly used by Royal Mail
for their Cleanmail Service. It is also called RM4SCC.

The constructor accepts one optional argument:

- A boolean `$useChecksum` value: used to enable or disable checksum validation.
  The value is `true` by default.

### SSCC

SSCC is the shortcut for "Serial Shipping Container Code".

This barcode is a variant of EAN barcodes. It must have a length of 18
characters, and supports only digits. The last digit must be a checksum digit
which is calculated with modulo 10. It is commonly used by the transport
industry.

The constructor accepts one optional argument:

- A boolean `$useChecksum` value: used to enable or disable checksum validation.
  The value is `true` by default.

### UPCA

UPC is the shortcut for "Universal Product Code".

This barcode preceded [EAN13](#ean13). It must have a length of 12 characters,
and supports only digits. The last digit must be a checksum digit which is
calculated with modulo 10. It is commonly used within the USA.

The constructor accepts one optional argument:

- A boolean `$useChecksum` value: used to enable or disable checksum validation.
  The value is `true` by default.

### UPCE

UPCE is the short variant from [UPCA](#upca).

This barcode is a smaller variant of UPCA. It can have a length of 6, 7, or 8
characters, and supports only digits. When the barcode is 8 characters long, it
includes a checksum which is calculated with modulo 10. It is commonly used with
small products where a [UPCA](#upca) barcode would not fit.

## Writing custom adapters

When working with proprietary barcode types, you may need to write a custom
barcode validator. To write your own barcode validator, you need the following
information.

- The length your barcode must have. You may set the length within the
  constructor using the method `setLength()` (which is `protected`). By default,
  we allow any of the following length values:
    - Any integer value greater than `0`, which means that the barcode **must**
      have this length.
    - An array of integer values. The length of this barcode must match
      one of the array values exactly.
    - `-1` as a value means there is no limitation for the length of this barcode.
    - `"even"` as a value means the length of this barcode must have an even
      amount of digits.
    - `"odd"` as a value means the length of this barcode must have an odd amount
      of digits.
- A string containing all allowed characters for this barcode.  The integer
  value `128` is also allowed, and is equivalent to the first 128 characters
  of the ASCII table. You may set the character set via the `protected` method
  `setCharacters()` within the constructor.
- If a checksum is possible, you will need to define logic for validating the
  checksum.

Your custom barcode validator must extend
`Zend\DataValidator\Barcode\AbstractAdapter` or implement
`Zend\DataValidator\Barcode\AdapterInterface`. Additionally, if you are
providing checksum validation, you will need to implement
`Zend\DataValidator\Barcode\ChecksummableInterface` (of which most details are
covered via the trait `Zend\DataValidator\Barcode\ChecksumTrait`).

As an example, let's create a validator that expects an even number of
characters that include all digits and the letters 'ABCDE', and which requires a
checksum.

```php
namespace My\Barcode;

use Zend\DataValidator\Barcode;
use Zend\DataValidator\Barcode\AbstractAdapter;
use Zend\DataValidator\Barcode\ChecksummableInterface;
use Zend\DataValidator\Barcode\ChecksumTrait;

class MyBar extends AbstractAdapter implements ChecksummableInterface
{
    use ChecksumTrait;

    protected $length     = 'even';
    protected $characters = '0123456789ABCDE';
    protected $checksum   = 'mod66';

    public function __construct()
    {
        $this->useChecksum = true;
        $this->checksumCallback = [$this, 'validateMod66Checksum'];
        $this->setLength('even');
        $this->setCharacters('0123456789ABCDE');
    }

    private function validateMod66Checksum(string $value) : bool
    {
        // do some validations and return a boolean
    }
}

$validator = new Barcode(new MyBar());
$result = $validator->validate($input);
if ($result->isValid()) {
    // input appears to be valid
} else {
    // input is invalid
}
```
