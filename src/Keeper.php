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

/**
 * Class Keeper
 *
 * Keep and assign the money.
 * The amount should be integer to avoid precision problem.
 *
 * @package Limen\RedEnvelope
 *
 * @author LI Mengxiang <limengxiang876@gmail.com>
 */
class Keeper
{
    /**
     * amount which could be negative
     * @var int
     */
    protected $amount;

    /**
     * @var int
     */
    protected $dividend;

    /**
     * @var float
     */
    protected $varianceFactor;

    /**
     * Keeper constructor.
     * @param int $amount
     * @param int $dividend
     * @param float $varianceFactor
     */
    public function __construct($amount = null, $dividend = null, $varianceFactor = null)
    {
        $this->amount = $amount ? $this->checkAmount($amount) : 0;
        $this->dividend = $dividend ? $this->checkDividend($dividend) : 0;
        $this->varianceFactor = $varianceFactor ? $this->checkVarianceFactor($varianceFactor) : 0;
    }

    /**
     * @param int $amount
     *
     * @throws \Exception
     *
     * @return $this
     */
    public function setAmount($amount)
    {
        $this->amount = $this->checkAmount($amount);

        return $this;
    }

    /**
     * @param int $dividend
     *
     * @return $this
     *
     * @throws \Exception
     */
    public function setDividend($dividend)
    {
        $this->dividend = $this->checkDividend($dividend);

        return $this;
    }

    /**
     * @param float $factor
     * @return $this
     * @throws \Exception
     */
    public function setVarianceFactor($factor)
    {
        $this->varianceFactor = $this->checkVarianceFactor($factor);

        return $this;
    }

    /**
     * Assign money
     * @return bool|float
     */
    public function assign()
    {
        $dividend = $this->dividend;
        $remain = abs($this->amount);

        $isNegative = $this->amount < 0;

        if ($remain == 0 || $dividend == 0) {
            return 0;
        }

        if ($remain == $dividend) {
            $get = 1;
        } else {
            $get = $this->randomAmount($remain, $dividend);
        }

        $get = $isNegative ? -$get : $get;

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
                $avg = floor($remain / $dividend);
                $get = $avg + $this->getNoise($avg);
                // make sure everyone can get at least a penny
                if ($get <= 0) {
                    $get = 1;
                } elseif ($get > $remain - $dividend + 1) {
                    $get = $remain - $dividend + 1;
                }
            }
            if ($i++ == $randIndex) {
                break;
            }
            $dividend -= 1;
            $remain -= $get;
        }

        return (int)$get;
    }

    /**
     * @param int $avg
     * @return int
     */
    protected function getNoise($avg)
    {
        $range = floor($avg * $this->varianceFactor);

        return rand(-$range, $range);
    }

    protected function checkAmount($amount)
    {
        if (!is_int($amount)) {
            Exception::pop(Exception::ERROR_AMOUNT_ILLEGAL);
        }

        return $amount;
    }

    protected function checkDividend($dividend)
    {
        if (!is_int($dividend) || $dividend <= 0) {
            Exception::pop(Exception::ERROR_DIVIDEND_ILLEGAL);
        }

        return $dividend;
    }

    protected function checkVarianceFactor($factor)
    {
        if (!is_float($factor) || $factor < 0) {
            Exception::pop(Exception::ERROR_VARIANCE_FACTOR_ILLEGAL);
        }

        return $factor;
    }
}