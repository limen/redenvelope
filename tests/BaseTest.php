<?php
/**
 * @author LI Mengxiang
 * @email lmx@yiban.cn
 * @since 2017/3/6 15:46
 */

use PHPUnit\Framework\TestCase;

class BaseTest extends TestCase
{
    public function testSomething()
    {
        $this->assertTrue(true);
    }

    protected function getMinAssignment($precision, $amount, $dividend)
    {
        $min = round(rand(1,10000) / 1000, $precision);

        if ($min === 0 || $amount < $min * $dividend) {
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