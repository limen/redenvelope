# PHP library for red envelope (hongbao) what is popular among Chinese. 

## Features

+ Amount is assigned randomly.
+ Minimum value of each assignment is settable.
+ Amount precision is settable.
+ Variance factor is settable which would influence the variance of the assignment sequence.
+ Use lock to avoid conflicts caused by concurrency.

## Installation

This library could be found on [Packagist](https://packagist.org/packages/limen/redenvelope "") for an easier management of projects dependencies using [Composer](https://getcomposer.org/ "").

## Usage

```php
use Limen\RedEnvelope\Envelope;
use Limen\RedEnvelope\Keeper;
use Limen\RedEnvelope\Lock\RedLock;

$id = '123';                    // unique id
$remain = 212.23;               // envelope remain amount
$dividend = 10;                 // how many fragments to divide amount into
$minAssignment = 10.1;              // minimum value of each assignment

$varianceFactor = 1.1;          // Appropriate value should between 0.5 and 1.5, 1.1 may be the best.
                                // The greater this value, the greater the variance of the divided sequence is.

$maxRetryTimes = 100;           // Max times to try to lock the envelope when envelope is locked
$ttl = 1000;                    // In term of millisecond, used as lock ttl when there would be potential unexpected error

$envelope = new Envelope($id);
$lock = new RedLock();          // Default lock which utilizes redis "setnx"

// lock envelope at first
if (!$envelope->setLock($lock)->lock($maxRetryTimes, $ttl)) {
    // alert and exit
}

/**
 *
 * do something to get remain and dividend
 *
 */

//set remain, dividend
$envelope->setRemain($remain)
    ->setDividend($dividend);

$keeper = new Keeper(null, null, $varianceFactor);         // Keeper who keep and assign money

// Set min amount keeper can assign. Default is 0.01
$keeper->setMinAssignment($minAssignment);
$envelope->setKeeper($keeper);

$amount = $envelope->open();
$remain = $envelope->getRemain();
$dividend = $envelope->getDividend();

/**
 *
 * do something to update database
 *
 */

// unlock envelope finally
$envelope->unlock();

///////////////
//
// test Keeper
//
///////////////
function float_equal($a, $b)
{
    return abs($a - $b) < 0.00001;
}

$total = 212.218;
$precision = 3;             // Keeper's precision. Could be 0, 1, 2 or 3
$dividend = 10;
$minAssignment = 10.1;
$varianceFactor = 1.0;
$keeper = new Keeper($total, $dividend, $varianceFactor);
$keeper->setMinAssignment($minAssignment)->setPrecision($precision);

$amounts = [];

for ($i = 0; $i < $dividend; $i++) {
    $assignment = $keeper->assign();
    if ($assignment >= $minAssignment) {
        $amounts[] = $assignment;
    } else {
        // shit happens
    }
}

$sum = array_sum($amounts);

if (!float_equal($total, $sum)) {
    // shit happens
    echo("Shit happens");
} else {
    // all right
}

var_dump($total, $amounts, $sum);
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
