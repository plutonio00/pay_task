<?php

namespace tests\unit\fixtures;


class Constants
{
    public const USER_COUNT = 10;
    public const WALLET_COUNT = self::USER_COUNT * 4;
    public const TRANSFER_COUNT = self::USER_COUNT * 20;
    public const DATE_TIME_FORMAT = 'Y-m-d H:i:00';
    public const DOUBLE_USER_COUNT = Constants::USER_COUNT * 2;
}