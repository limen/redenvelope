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

use Limen\RedEnvelope\Lock\LockInterface;

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
     * Envelope lock
     * @var LockInterface
     */
    protected $lock;

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
        $this->keeper->setAmount($this->remain)
            ->setDividend($this->dividend);

        $amount = $this->keeper->assign();

        $this->dividend--;
        $this->remain -= $amount;

        return $amount;
    }

    /**
     * Lock envelope
     * @param $maxRetryTimes
     * @param $ttl
     * @return mixed
     */
    public function lock($maxRetryTimes, $ttl)
    {
        return $this->lock
            ->setId($this->id)
            ->setMaxRetryTimes($maxRetryTimes)
            ->setTtl($ttl)
            ->lock();
    }

    /**
     * Unlock envelope
     * @return mixed
     */
    public function unlock()
    {
        return $this->lock->unlock();
    }

    /**
     * @param LockInterface $lock
     * @return $this
     */
    public function setLock($lock)
    {
        $this->lock = $lock;

        return $this;
    }

    /**
     * @param Keeper $keeper
     * @return $this
     */
    public function setKeeper($keeper)
    {
        $this->keeper = $keeper;

        return $this;
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

}