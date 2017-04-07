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
 * Class Envelope
 *
 * @package Limen\RedEnvelope
 *
 * @author LI Mengxiang <limengxiang876@gmail.com>
 */
class Envelope
{
    /**
     * Envelope id which should be unique
     * @var string|int
     */
    protected $id;

    /**
     * Remain amount
     * @var float
     */
    protected $remain;

    /**
     * How many parts to divide envelope remain into
     * @var int
     */
    protected $dividend;

    /**
     * Keep and assign envelope remain
     * @var Keeper
     */
    protected $keeper;

    /**
     * Envelope constructor.
     * @param string|int $id
     * @param float $remain
     * @param int $dividend
     */
    public function __construct($id = null, $remain = null, $dividend = null)
    {
        $this->id = $id;
        $this->remain = $remain;
        $this->dividend = (int)$dividend;
        $this->keeper = new Keeper();
    }

    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }

    public function getId()
    {
        return $this->id;
    }

    /**
     * Open the envelope
     * @return float
     */
    public function open()
    {
        if ($this->keeper === null) {
            $this->keeper = new Keeper();
        }

        $this->keeper->setAmount($this->remain)
            ->setDividend($this->dividend);

        $amount = $this->keeper->assign();

        $this->dividend--;
        $this->remain -= $amount;

        return $amount;
    }

    /**
     * @param KeeperContract $keeper
     * @return $this
     */
    public function setKeeper($keeper)
    {
        $this->keeper = $keeper;

        return $this;
    }

    public function getKeeper()
    {
        return $this->keeper;
    }

    /**
     * @param float $remain
     * @return $this
     */
    public function setRemain($remain)
    {
        $this->remain = $remain;

        return $this;
    }

    /**
     * @param int $dividend
     * @return $this
     */
    public function setDividend($dividend)
    {
        $this->dividend = $dividend;

        return $this;
    }

    /**
     * @return float
     */
    public function getRemain()
    {
        return $this->remain;
    }

    /**
     * @return int|null
     */
    public function getDividend()
    {
        return $this->dividend;
    }

    public function __call($method, $args)
    {
        if (method_exists($this->keeper, $method)) {
            return call_user_func_array([$this->keeper, $method], $args);
        }

        throw new Exception("Call an undefined method \"$method\"");
    }
}