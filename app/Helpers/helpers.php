<?php

use App\Models\Setting;

if (!function_exists('format_currency')) {
    /**
     * Format a number as currency using app settings.
     */
    function format_currency(string|float|int|null $amount): string
    {
        $symbol = Setting::get('currency_symbol', '৳');
        $amount = number_format((float) ($amount ?? 0), 2);

        return $symbol . ' ' . $amount;
    }
}

if (!function_exists('currency_symbol')) {
    /**
     * Get the currency symbol.
     */
    function currency_symbol(): string
    {
        return Setting::get('currency_symbol', '৳');
    }
}

if (!function_exists('app_date_format')) {
    /**
     * Get the application date format.
     */
    function app_date_format(): string
    {
        return Setting::get('date_format', 'd M Y');
    }
}

if (!function_exists('get_setting')) {
    /**
     * Get a setting value by key.
     */
    function get_setting(string $key, mixed $default = null): mixed
    {
        return Setting::get($key, $default);
    }
}

