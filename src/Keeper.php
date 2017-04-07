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
use Limen\RedEnvelope\Contracts\KeeperContract;

/**
 * Class Keeper
 * Keep and assign the money.
 *
 * @package Limen\RedEnvelope
 *
 * @author LI Mengxiang <limengxiang876@gmail.com>
 */
class Keeper extends KeeperContract
{
    /**
     * Assign money
     * @return bool|float
     */
    public function assign()
    {
        $this->amount = $this->cutByPrecision($this->amount);
        $this->minAssignment = $this->cutByPrecision($this->minAssignment);

        $this->checkAmountEnough();

        if ($this->amount == 0 || $this->dividend == 0) {
            return 0;
        }

        $get = $this->randomAmount($this->amount, $this->dividend);

        $get = $this->cutByPrecision($get);

        $this->amount -= $get;
        $this->dividend--;

        return $get;
    }

    /**
     * Get random amount from remain
     * @param int $remain
     * @param int $dividend
     * @return int
     */
    protected function randomAmount($remain, $dividend)
    {
        $randIndex = rand(0, $dividend - 1);

        $i = 0;
        $get = 0;

        while ($dividend > 0) {
            if ($dividend == 1) {
                $get = $remain;
            } else {
                $avg = $remain / $dividend;
                $max = $remain - $this->minAssignment * ($dividend - 1);
                $get = $avg + $this->getNoise($avg);

                if ($get < $this->minAssignment) {
                    $get = $this->minAssignment;
                } elseif ($get > $max) {
                    $get = $max;
                }
            }
            if ($i++ == $randIndex) {
                break;
            }
            $dividend -= 1;
            $remain -= $get;
        }

        return $get;
    }

    /**
     * @param int $avg
     * @return int
     */
    protected function getNoise($avg)
    {
        $avg = abs($this->powForward($avg));

        $range = floor($avg * $this->varianceFactor);

        return $this->powBackward(rand(-$range, $range));
    }

    /**
     * @param $amount
     * @return mixed
     */
    protected function cutByPrecision($amount)
    {
        return round($amount, $this->precision);
    }

    /**
     * @param $value
     * @return int
     */
    protected function powForward($value)
    {
        return (int)($value * pow(10, $this->precision));
    }

    /**
     * @param $value
     * @return float
     */
    protected function powBackward($value)
    {
        return round($value * pow(10, -$this->precision), $this->precision);
    }
}