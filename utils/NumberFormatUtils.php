<?php


namespace app\utils;


class NumberFormatUtils
{
    public static function formatAmount(float $amount): float {
        return number_format($amount, 2, '.', '');
    }
}