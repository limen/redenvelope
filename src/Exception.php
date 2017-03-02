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
 * Class Exception
 * @package Limen\RedEnvelope
 *
 * @author LI Mengxiang <limengxiang876@gmail.com>
 */
class Exception extends \Exception
{
    const ERROR_REMAIN_EMPTY = 1;
    const ERROR_DIVIDEND_ZERO = 2;
    const ERROR_ENVELOPE_LOCKED = 3;
    const ERROR_AMOUNT_ILLEGAL = 4;
    const ERROR_DIVIDEND_ILLEGAL = 5;
    const ERROR_VARIANCE_FACTOR_ILLEGAL = 6;

    public static function pop($code)
    {
        throw new static(static::getErrMsg($code), $code);
    }

    protected static function getErrMsg($code)
    {
        $msg = 'Unknown error';

        switch ($code) {
            case static::ERROR_REMAIN_EMPTY:
                $msg = 'Remain is empty.';
                break;
            case static::ERROR_DIVIDEND_ZERO:
                $msg = 'Dividend is empty.';
                break;
            case static::ERROR_ENVELOPE_LOCKED:
                $msg = 'Envelope is locked.';
                break;
            case static::ERROR_AMOUNT_ILLEGAL:
                $msg = 'Illegal amount which should be integer.';
                break;
            case static::ERROR_DIVIDEND_ILLEGAL:
                $msg = 'Illegal dividend which should be integer and gt zero.';
                break;
            case static::ERROR_VARIANCE_FACTOR_ILLEGAL:
                $msg = 'Illegal variance factor which should be numeric and gte zero.';
                break;
            default:
                break;
        }

        return $msg;
    }
}