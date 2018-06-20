# NotEmpty Validator

This validator allows you to validate if a given value is not empty. This is
often useful when working with form elements or other user input, where you can
use it to ensure required elements have values associated with them.

## Instantiation

The constructor to `Zend\DataValidator\NotEmpty` accepts a single value,
representing a _type mask_ of PHP types that the validator will check for
emptiness. The value may be one of:

- An integer representing the entire bitmask.
- A descriptive string representing a single bit to provide to the bitmask.
- An array of integers (or strings as noted above) to `|` together to form the
  bitmask.

The following types are supported:

Constant<sup>*</sup> | String equivalent | Bitmask          | Description
-------------------- | ----------------- | ---------------- | -----------
`BOOLEAN`            | boolean           | `0b000000000001` | Evaluate boolean values; false is then treated as empty.
`INTEGER`            | integer           | `0b000000000010` | Evaluate integer values; `0` is then treated as empty.
`FLOAT`              | float             | `0b000000000100` | Evaluate float/double values; `0.0` is then treated as empty.
`STRING`             | string            | `0b000000001000` | Evaluate string values; `''` is then treated as empty.
`ZERO`               | zero              | `0b000000010000` | Evaluate a string zero value; `'0'` is then treated as empty.
`EMPTY_ARRAY`        | array             | `0b000000100000` | Evaluate array values; `[]` is then treated as empty.
`NULL`               | null              | `0b000001000000` | Evaluate null values; `null` is then treated as empty.
`PHP`                | php               | `0b000001111111` | Evaluate all PHP scalar types and arrays, treating matching values as empty per the rules above.
`SPACE`              | space             | `0b000010000000` | Evaluate string values; strings consisting solely of whitespace are then treated as empty.
`OBJECT`             | object            | `0b000100000000` | Evaluate object values; objects are NEVER considered empty (unless they violate other rules in the mask).
`OBJECT_STRING`      | objectstring      | `0b001000000000` | Evaluate object values; objects that cannot be cast to string, or evaluate to `''` when cast, are treated as empty.
`OBJECT_COUNT`       | objectcount       | `0b010000000000` | Evaluate object values; objets that do not implement `Countable`, or evaluate to `0` when cast, are treated as empty.
`ALL`                | all               | `0b011111111111` | Apply all rules.


> <sup>*</sup> All constants are defined on the `Zend\DataValidator\NotEmpty` class.

You may combine rules using one of three approaches:

- Provide them in an array. String values in the array are converted to the
  equivalent bitmask. All types are then combined using `|`.
- Use bitwise operators to combine bitmask values or constant values;
  e.g., `NotEmpty::BOOLEAN | NotEmpty::NULL`.
- Use arithmetic operations to combine bitmask values; e.g. `NotEmpty::BOOLEAN +
  NotEmpty::NULL`. (Bitwise operators are safest!)


## Default behaviour

By default, if now type bitmask is provided, the `NotEmpty` validator works
differently than you would expect when you've worked with PHP's `empty()`
operator. The default bitmask is `NotEmpty::OBJECT | NotEmpty::SPACE |
NotEmpty::NULL | NotEmpty::EMPTY_ARRAY | NotEmpty::STRING | NotEmpty::BOOLEAN`.
This means that it treats any of the following values as empty:

- A string consisting of solely whitespace
- `null`
- An empty array (`[]`)
- An empty string (`''`)
- Boolean `false`.

Objects are considered non-empty.

## Usage

As with other validators, you will create an instance, and then call
`validate()` on it, passing the value you wish to validate. This produces a
result instance, which you can then query for the status of validation.

```php
use Zend\DataValidator\NotEmpty;

$value  = '';
$validator = new NotEmpty();
$result = $validator->validate($value);

// $result->isValid() returns false

// Value `0` considered invalid:
$validator = new NotEmpty(NotEmpty::INTEGER);

// Value `0` or `'0'` considered invalid:
$validator = new NotEmpty(NotEmpty::INTEGER | NotEmpty::ZERO);

// Same way of expressing the above:
$validator = new NotEmpty([NotEmpty::INTEGER, NotEmpty::ZERO]);

// Still another way to express it:
$validator = new NotEmpty(['integer', 'zero']);
```
