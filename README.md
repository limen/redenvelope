# PHP library for red envelope (hongbao) what is popular among Chinese.

[![Build Status](https://travis-ci.org/limen/redenvelope.svg?branch=master)](https://travis-ci.org/limen/redenvelope)
[![Packagist](https://img.shields.io/packagist/l/limen/redenvelope.svg?maxAge=2592000)](https://packagist.org/packages/limen/redenvelope)

## Features

+ Amount is assigned randomly.
+ Minimum value of each assignment is settable.
+ Amount precision is settable.
+ Variance factor is settable which would influence the variance of the assignment sequence.

## Installation

Recommend to install via [composer](https://getcomposer.org/ "").

```bash
composer require "limen/redenvelope"
```

## Usage

```php
use Limen\RedEnvelope\Envelope;
use Limen\RedEnvelope\Keeper;
use Limen\RedEnvelope\Lock\RedLock;

$id = '123';                    // unique id
$remain = 212.23;               // envelope remain amount
$dividend = 10;                 // how many fragments to divide amount into
$minAssignment = 10.1;          // minimum value of each assignment
$precision = 2;                 // assignment precision

$varianceFactor = 1.0;          // Appropriate value should between 0.5 and 1.5, 1.0 may be the best.
                                // The greater this value, the greater the variance of the divided sequence is.

$envelope = new Envelope($id);

$envelope->setRemain($remain)
    ->setDividend($dividend);
    ->setPrecision($precision)
    ->setMinAssignment($minAssignment)
    ->setVarianceFactor($varianceFactor);

$assignment = $envelope->open();
```

## Development

### Test

```bash
$ phpunit --bootstrap tests/bootstrap.php tests/
PHPUnit 5.7.15 by Sebastian Bergmann and contributors.

..
Total amount: 214.4
Dividend: 20
Minimum Assignment: 0.86
Variance factor: 0.7
Precision: 2
Assignment value: 10.72
..
Total amount: 64
Dividend: 4
Minimum Assignment: 7
Variance factor: 1.5
Precision: 0
Assignments: 
No.0: 19.000000
No.1: 15.000000
No.2: 7.000000
No.3: 23.000000
.                                                               5 / 5 (100%)

Time: 165 ms, Memory: 13.25MB

OK (5 tests, 36 assertions)
```
