# UUID Validator

`Zend\DataValidator\Uuid` allows validating [Universally Unique IDentifiers](https://en.wikipedia.org/wiki/Universally_unique_identifier)
(UUIDs). UUIDs are 128-bit values that are guaranteed to be "practically unique"
in order to help prevent identifier conflicts. Five separate UUID versions
exist:

- Version 1, which uses a combination of date-time and hardware MAC addresses to
  generate the hash.
- Version 2, which uses a combination of date-time and system user/group identifiers.
- Version 3, which uses an MD5sum of a URI or distinguished name to generate the
  hash.
- Version 4, which uses a CSPRNG to generate the hash.
- Version 5, which uses the same idea as Version 3, but using SHA-1 for hashing.

The `Uuid` validator is capable of validating whether a string is a valid UUID
of any version. It does not validate that the UUID exists in your system,
however, only that it is well-formed.

## Supported options

The `Uuid` validator has no additional options.

## Basic usage

```php
$validator = new Zend\DataValidator\Uuid();
$result = $validator->validate($uuid);

if ($result->isValid()) {
    // UUID was valid
} else {
    // Invalid/mal-formed UUID; use $result->getMessages() for more detail
}
```
