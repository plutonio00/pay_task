<?php

namespace app\utils;

class ArrayUtils
{
    public static function addPrefixToAllKeys(string $prefix, array $array): array
    {
        foreach ($array as $key => $value) {
            $array[$prefix . $key] = $value;
            unset($array[$key]);
        }

        return $array;
    }
}