# PHP library for red envelope (hongbao) what is popular among Chinese. 

## Features

+ Amount is assigned randomly.
+ Variance factor could be adjusted on the fly which would influence the variance of the assignment sequence.
+ Use lock to avoid conflict caused by concurrency.

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
$minAmount = 10.1;              // minimum amount of each assignment

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
$keeper->setMinAmount($minAmount);
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
$minAmount = 10.1;
$varianceFactor = 1.0;
$keeper = new Keeper($total, $dividend, $varianceFactor);
$keeper->setMinAmount($minAmount)->setPrecision($precision);

$amounts = [];

for ($i = 0; $i < $dividend; $i++) {
    $assignment = $keeper->assign();
    if ($assignment >= $minAmount) {
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
