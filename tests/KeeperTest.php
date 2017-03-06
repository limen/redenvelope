<?php
/*
 * This file is part of the Redenvelope package.
 *
 * (c) LI Mengxiang <limengxiang876@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use Limen\RedEnvelope\Keeper;
use PHPUnit\Framework\TestCase;

/**
 * Class KeeperTest
 *
 * @author LI Mengxiang <limengxiang876@gmail.com>
 */
class KeeperTest extends BaseTest
{
    public function testAssignment()
    {
        $keeper = new Keeper();

        $precision = $this->getPrecision();

        $amount = $this->getAmount($precision);
        $dividend = $this->getDividend();
        $minAssignment = $this->getMinAssignment($precision, $amount, $dividend);
        $vf = $this->getVarianceFactor();

        echo PHP_EOL;
        echo "Total amount: " . $amount . PHP_EOL;
        echo "Dividend: " . $dividend . PHP_EOL;
        echo "Minimum Assignment: " . $minAssignment . PHP_EOL;
        echo "Variance factor: " . $vf . PHP_EOL;
        echo "Precision: " . $precision . PHP_EOL;

        $keeper->setAmount($amount)
            ->setDividend($dividend)
            ->setPrecision($precision)
            ->setMinAssignment($minAssignment)
            ->setVarianceFactor($vf);
        $assignments = [];
        for ($i = 0; $i < $dividend; $i++) {
            $remain = $keeper->getAmount();
            $assignment = $keeper->assign();
            $this->assertGreaterThanOrEqual($minAssignment, $assignment);
            $this->assertGreaterThan(0, $assignment);
            $this->assertEquals($keeper->getDividend(), $dividend - 1 - $i);
            $this->assertEquals($keeper->getAmount(), $remain - $assignment);
            $assignments[] = $assignment;
        }

        echo 'Assignments: ' . PHP_EOL;
        foreach ($assignments as $k => $value) {
            printf('No.%d: %f' . PHP_EOL, $k, $value);
        }

        $this->assertEquals(array_sum($assignments), $amount);
        $this->assertEquals($keeper->getAmount(), 0);
        $this->assertEquals($keeper->getDividend(), 0);
        $this->assertEquals($keeper->assign(), 0);
        $this->assertEquals($keeper->getAmount(), 0);
        $this->assertEquals($keeper->getDividend(), 0);
    }

}