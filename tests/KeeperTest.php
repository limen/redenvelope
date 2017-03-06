<?php
/*
 * This file is part of the Redenvelope package.
 *
 * (c) LI Mengxiang <limengxiang876@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Limen\RedEnvelope;

use PHPUnit\Framework\TestCase;

$baseDir = dirname(dirname(__FILE__));

require_once($baseDir . "/src/Keeper.php");
require_once($baseDir . "/src/Exception.php");

/**
 * Class KeeperTest
 *
 * @author LI Mengxiang <limengxiang876@gmail.com>
 */
class KeeperTest extends TestCase
{
    public function testAssignment()
    {
        $keeper = new Keeper();

        $precision = $this->getPrecision();

        $amount = $this->getAmount($precision);
        $dividend = $this->getDividend();
        $minAssignment = $this->getMinAssignment($precision);
        $vf = $this->getVarianceFactor();

        echo PHP_EOL;
        echo "Total amount: " . $amount . PHP_EOL;
        echo "Dividend: " . $dividend . PHP_EOL;
        echo "Minimum Assignment: " . $minAssignment . PHP_EOL;
        echo "Variance factor: " . $vf . PHP_EOL;
        echo "Precision: " . $precision . PHP_EOL;

        try {
            $keeper->setAmount($amount)
                ->setDividend($dividend)
                ->setPrecision($precision)
                ->setMinAssignment($minAssignment)
                ->setVarianceFactor($vf);
            $assignments = [];
            for ($i = 0; $i<$dividend; $i++) {
                $assignment = $keeper->assign();
                $this->assertGreaterThanOrEqual($minAssignment, $assignment);
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
        } catch (Exception $e) {
            $this->assertEquals($e->getCode(), Exception::ERROR_AMOUNT_NOT_ENOUGH);
            echo $e->getMessage() . PHP_EOL;
        }
    }

    protected function getMinAssignment($precision)
    {
        $min = round(rand(1,10000) / 1000, $precision);

        if ($min === 0) {
            $min = pow(1, -$precision);
        }

        return $min;
    }

    protected function getAmount($precision)
    {
        return round(rand(1000,100000) / 100, $precision);
    }

    protected function getDividend()
    {
        return rand(1,20);

    }

    protected function getPrecision()
    {
        return rand(0,3);
    }

    protected function getVarianceFactor()
    {
        return rand(5,15) / 10;
    }
}