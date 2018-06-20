# zend-datavalidator

[![Build Status](https://secure.travis-ci.org/zendframework/zend-datavalidator.svg?branch=master)](https://secure.travis-ci.org/zendframework/zend-datavalidator)
[![Coverage Status](https://coveralls.io/repos/github/zendframework/zend-datavalidator/badge.svg?branch=master)](https://coveralls.io/github/zendframework/zend-datavalidator?branch=master)

> ## UNSTABLE
>
> This is a draft library that will eventually be included in Zend Framework. It
> is currently unstable, and likely should not be used in production. Use at
> your own risk!

This library provides general purpose, stateless validation for PHP values.

Design document: https://discourse.zendframework.com/t/rfc-new-validation-component/208

## Installation

Run the following to install this library:

```bash
$ composer require zendframework/zend-datavalidator
```

> Note: this package is not yet on Packagist; you will need to add a repository
> entry to your `composer.json` referencing this github repo in order to
> complete installation.

## Documentation

Documentation is [in the doc tree](docs/book/), and can be compiled using [mkdocs](http://www.mkdocs.org):

```bash
$ mkdocs build
```

~~You may also [browse the documentation online](https://docs.zendframework.com/zend-datavalidator/).~~
