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

use Limen\RedModel\Model as RedModel;

/**
 * Class RedLock
 *
 * Lock for avoiding concurrency conflict
 *
 * @package Limen\RedEnvelope\Lock
 *
 * @author LI Mengxiang <limengxiang876@gmail.com>
 */
class RedLock extends RedModel implements LockInterface
{
    /**
     * Red envelope id to lock
     * @var mixed
     */
    protected $id;

    protected $type = RedModel::TYPE_STRING;

    protected $key = 'red_envelope:{id}:lock';

    /**
     * Max retry times to lock
     * @var int
     */
    protected $maxRetryTimes = 100;

    /**
     * lock ttl in millisecond
     * @var int
     */
    protected $ttl = 1000;

    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }

    /**
     * @param array $parameters
     * @param array $options
     */
    public function initRedisClient($parameters, $options = null)
    {
        if (!isset($parameters['host']) || empty($parameters['host'])) {
            $parameters['host'] = 'localhost';
        }
        if (!isset($parameters['port']) || empty($parameters['port'])) {
            $parameters['port'] = 6379;
        }

        parent::initRedisClient($parameters, $options);
    }

    /**
     * @param int $times
     * @return $this
     */
    public function setMaxRetryTimes($times)
    {
        $this->maxRetryTimes = $times;

        return $this;
    }

    /**
     * @param int $ttl
     * @return $this
     */
    public function setTTL($ttl)
    {
        $this->ttl = $ttl;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return int
     */
    public function getMaxRetryTimes()
    {
        return $this->maxRetryTimes;
    }

    /**
     * @return int
     */
    public function getTtl()
    {
        return $this->ttl;
    }

    /**
     * Lock item
     * @return bool return true when success
     */
    public function lock()
    {
        $success = false;

        for ($i = 0; $i < $this->maxRetryTimes; $i++) {
            if ($this->where('id', $this->id)->setnx(1)) {
                $this->where('id', $this->id)->pexpire($this->getTtl());
                $success = true;
                break;
            }
        }

        return $success;
    }

    /**
     * Unlock item
     * @return bool
     */
    public function unlock()
    {
        return $this->destroy($this->id);
    }

    /**
     * Check if item locked
     * @return bool
     */
    public function isLocked()
    {
        return (bool)$this->find($this->id);
    }
}