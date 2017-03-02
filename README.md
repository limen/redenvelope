# PHP library for red envelope (hongbao) what is popular among Chinese. 

## Features

+ Amount could be positive or negative.
+ Variance factor could be adjusted on the fly.
+ Utilize lock to avoid conflict caused by concurrency.

## Installation

This library could be found on [Packagist](https://packagist.org/packages/limen/redenvelope "") for an easier management of projects dependencies using [Composer](https://getcomposer.org/ "").

## Usage

```php
use Limen\RedEnvelope\Envelope;
use Limen\RedEnvelope\Keeper;
use Limen\RedEnvelope\Lock\RedLock;

$id = '123';                    // unique id
$remain = 999.99;               // envelope remain amount
$dividend = 10;                 // how many fragments to divide amount into

$varianceFactor = 1.1;          // Appropriate value should between 0.5 and 1.5, 1.1 may be the best.
                                // The greater this value is, the greater the variance of the divided sequence would be.

$maxRetryTimes = 100;           // Max retry times to lock the envelope when envelope is locked by another request
$ttl = 1000;                    // In terms of millisecond, used as lock ttl when there would be potential unexpected error

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

//set remain and dividend
$envelope->setRemain($remain)->setDividend($dividend);

$keeper = new Keeper(null, null, $varianceFactor);         // Keeper who keep and assign money
$envelope->setKeeper($keeper);

$amount = $envelope->open();
$remain = $envelope->getRemain();
$dividend = $envelope->getDividend();

/**
 *
 * do something to transfer money or update database
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

$total = -99999;                // Negative value is acceptable.
$dividend = 10;
$keeper = new Keeper($total, $dividend, $varianceFactor);

$amounts = [];

for ($i = 0; $i < $dividend; $i++) {
    $assignment = $keeper->assign();
    if ($assignment < 0) {
        $amounts[] = $assignment;
    } else {
        // shit happens
    }
}

$sum = array_sum($amounts);

if (!float_equal($total, $sum)) {
    // shit happens
} else {
    // all right
}
```
