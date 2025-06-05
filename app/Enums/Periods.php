<?php

namespace App\Enums;

use Carbon\Carbon;

enum Periods:string
{
    case ONE_DAY = '1D';
    case ONE_MONTH = '1M';
    case THREE_MONTHS = '3M';
    case SIX_MONTHS = '6M';
    case YTD = 'YTD';
    case ONE_YEAR = '1Y';
    case THREE_YEARS = '3Y';
    case FIVE_YEARS = '5Y';
    case TEN_YEARS = '10Y';
    case MAX = 'MAX';


    public function getDate(Carbon $latest): ?Carbon
    {
        return match ($this) {
            self::ONE_DAY => $latest->copy()->subDay(),
            self::ONE_MONTH => $latest->copy()->subMonth(),
            self::THREE_MONTHS => $latest->copy()->subMonths(3),
            self::SIX_MONTHS => $latest->copy()->subMonths(6),
            self::YTD => $latest->copy()->startOfYear(),
            self::ONE_YEAR => $latest->copy()->subYear(),
            self::THREE_YEARS => $latest->copy()->subYears(3),
            self::FIVE_YEARS => $latest->copy()->subYears(5),
            self::TEN_YEARS => $latest->copy()->subYears(10),
            self::MAX => null,
        };
    }
}
