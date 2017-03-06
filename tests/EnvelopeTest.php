<?php
/*
 * This file is part of the Redenvelope package.
 *
 * (c) LI Mengxiang <limengxiang876@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use Limen\RedEnvelope\Envelope;
use Limen\RedEnvelope\Keeper;
use Limen\RedEnvelope\Lock\RedLock;

/**
 * Class EnvelopeTest
 *
 * @author LI Mengxiang <limengxiang876@gmail.com>
 */
class EnvelopeTest extends BaseTest
{
    public function testAssignment()
    {
        $envelope = new Envelope();

        $lock = new RedLock();
        $keeper = new Keeper();

        $precision = $this->getPrecision();
        $vf = $this->getVarianceFactor();
        $amount = $this->getAmount($precision);
        $dividend = $this->getDividend();
        $minAssignment = $this->getMinAssignment($precision, $amount, $dividend);

        echo PHP_EOL;
        echo "Total amount: " . $amount . PHP_EOL;
        echo "Dividend: " . $dividend . PHP_EOL;
        echo "Minimum Assignment: " . $minAssignment . PHP_EOL;
        echo "Variance factor: " . $vf . PHP_EOL;
        echo "Precision: " . $precision . PHP_EOL;

        $keeper->setPrecision($precision)->setMinAssignment($minAssignment);

        $envelope->setRemain($amount)
            ->setDividend($dividend)
            ->setKeeper($keeper)
            ->setLock($lock)
            ->lock(100, 2000);

        $assignment = $envelope->setRemain($amount)
            ->setDividend($dividend)
            ->setKeeper($keeper)
            ->setLock($lock)
            ->open();

        $this->assertGreaterThanOrEqual($minAssignment, $assignment);
        $this->assertEquals($amount - $assignment, $envelope->getRemain());
        $this->assertEquals($dividend - 1, $envelope->getDividend());

        $this->assertTrue($envelope->getLock()->isLocked());
        $this->assertFalse($envelope->lock(100, 2000));

        $envelope->unlock();

        $this->assertFalse($envelope->getLock()->isLocked());

        echo "Assignment value: " . $assignment . PHP_EOL;
    }

}