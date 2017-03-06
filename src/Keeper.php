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
     * Amount precision
     * @var int
     */
    protected $precision = 2;

    /**
     * @var int
     */
    protected $dividend;

    /**
     * @var float
     */
    protected $varianceFactor = 1.0;

    /**
     * Minimum value of each assignment
     * @var int
     */
    protected $minAssignment = 0.01;

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
        $amount = $this->cutByPrecision($amount);

        $this->amount = $this->checkAmount($amount);

        $this->checkAmountEnough();

        return $this;
    }

    /**
     * @param int $precision
     * @return $this
     */
    public function setPrecision($precision)
    {
        $this->precision = $this->checkPrecision($precision);

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

        $this->checkAmountEnough();

        return $this;
    }

    /**
     * @return float
     */
    public function getAmount()
    {
        return $this->cutByPrecision($this->amount);
    }

    /**
     * @return int
     */
    public function getDividend()
    {
        return $this->dividend;
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
     * @param int $min
     * @return $this
     */
    public function setMinAssignment($min)
    {
        $min = $this->cutByPrecision($min);

        $this->minAssignment = $this->checkMinAssignment($min);

        return $this;
    }

    /**
     * Assign money
     * @return bool|float
     */
    public function assign()
    {
        if ($this->amount == 0 || $this->dividend == 0) {
            return 0;
        }

        $get = $this->randomAmount($this->amount, $this->dividend);

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

        return $this->cutByPrecision($get);
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

    protected function checkAmount($amount)
    {
        if ((!is_float($amount) && !is_int($amount)) || $amount <= 0) {
            Exception::pop(Exception::ERROR_AMOUNT_ILLEGAL);
        }

        return $amount;
    }

    protected function checkPrecision($precision)
    {
        if (!is_int($precision) || $precision < 0 || $precision > 3) {
            Exception::pop(Exception::ERROR_PRECISION_ILLEGAL);
        }

        return $precision;
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
        if ( (!is_float($factor) && !is_int($factor))
            || $factor < 0
        ) {
            Exception::pop(Exception::ERROR_VARIANCE_FACTOR_ILLEGAL);
        }

        return $factor;
    }

    protected function checkMinAssignment($min)
    {
        if ((!is_float($min) && !is_int($min)) || $min <= 0) {
            Exception::pop(Exception::ERROR_MIN_ASSIGNMENT_ILLEGAL);
        }

        $this->checkAmountEnough();

        return $min;
    }

    protected function checkAmountEnough()
    {
        if ($this->amount
            && $this->dividend
            && $this->minAssignment
            && abs($this->amount) < abs($this->dividend * $this->minAssignment)
        ) {
            Exception::pop(Exception::ERROR_AMOUNT_NOT_ENOUGH);
        }
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