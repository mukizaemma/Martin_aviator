<?php

namespace App\Support;

use App\Models\Setting;

class Currency
{
    public static function rwfPerUsd(?float $override = null): float
    {
        if ($override !== null && $override > 0) {
            return (float) $override;
        }

        $setting = Setting::first();
        $rate = $setting?->usd_to_rwf_rate;

        return $rate > 0 ? (float) $rate : 1300.0;
    }

    public static function usdToRwf(float $usd, ?float $rate = null): float
    {
        return round($usd * self::rwfPerUsd($rate), 0);
    }

    /**
     * HTML span: USD with hover title showing RWF equivalent.
     */
    public static function formatUsdHover(float|string|null $usd, ?float $rate = null): string
    {
        if ($usd === null || $usd === '') {
            return '';
        }

        $usd = (float) $usd;
        $rwf = self::usdToRwf($usd, $rate);
        $usdFmt = number_format($usd, $usd == floor($usd) ? 0 : 2);
        $rwfFmt = number_format($rwf, 0, '.', ',');

        return '<span class="usd-price-hint" title="Approx. '.$rwfFmt.' RWF (rate from settings)">$'.$usdFmt.'</span>';
    }
}
