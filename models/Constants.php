<?php


namespace app\models;


class Constants
{
    public const INVALID_AMOUNT_MESSAGE = 'Amount must be a decimal number with 1 to 11 digits and 1 to 2 optional decimal places. Separate the whole part from the fractional part with the symbol  \'.\'';
    public const AMOUNT_PATTERN = '/^\d{1,11}(\.\d{1,2})?$/';
    public const CACHE_KEY_TRANSFER_DONE_STATISTIC = 'last_done_transfers';
}