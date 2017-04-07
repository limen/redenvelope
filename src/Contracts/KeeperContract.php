<?php

namespace Limen\RedEnvelope\Contracts;

use Limen\RedEnvelope\Exception;

abstract class KeeperContract
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

    public function getAmount()
    {
        return $this->amount;
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

        return $this;
    }

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
        $this->minAssignment = $this->checkMinAssignment($min);

        return $this;
    }

    /**
     * Assign money
     * @return bool|float
     */
    abstract public function assign();

    /**
     * @param $amount
     * @return mixed
     */
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
}