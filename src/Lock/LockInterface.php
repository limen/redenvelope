<?php
/*
 * This file is part of the Redenvelope package.
 *
 * (c) LI Mengxiang <limengxiang876@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Limen\RedEnvelope\Lock;

/**
 * Interface LockInterface
 * @package Limen\RedEnvelope\Lock
 *
 * @author LI Mengxiang <limengxiang876@gmail.com>
 */
interface LockInterface
{
    /**
     * lock id
     * @param $id
     * @return $this
     */
    public function setId($id);

    /**
     * @param $ttl
     * @return $this
     */
    public function setTtl($ttl);

    /**
     * @param $times
     * @return $this
     */
    public function setMaxRetryTimes($times);

    public function getId();

    public function getTtl();

    public function getMaxRetryTimes();

    public function lock();

    public function unlock();

    public function isLocked();
}