# Date Validator

`Zend\DataValidator\Date` allows you to validate if a given value is a
`DateTimeInterface` instance, or is a value that may be coerced to one.

## Default date validation

The easiest way to validate a date is by using the default date format,
`Y-m-d`.

```php
$validator = new Zend\DataValidator\Date();

$result1 = $validator->validate('2000-10-10');
echo $result1->isValid(); // True if valid

$result2 = $validator->validate('10.10.2000');
echo $result2->isValid(); // False if invalid
```

## Specifying a date format

`Zend\DataValidator\Date` also supports custom date formats. When you want to
validate such a date, pass the format to constructor. All formats used must be
compatible with the [DateTime::createFromFormat()](http://php.net/manual/en/datetime.createfromformat.php#refsect1-datetime.createfromformat-parameters) method.

```php
$validator = new Zend\DataValidator\Date('Y');

$result1 = $validator->validate('2010');
echo $result1->isValid(); // True

$result1 = $validator->validate('May');
echo $result2->isValid(); // False
```
