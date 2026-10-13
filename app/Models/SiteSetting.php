<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class SiteSetting extends Model
{
    protected $fillable = [
        'key',
        'value',
    ];

    private const CACHE_KEY = 'site_settings:all';

    /** Loaded once per request, so repeated get()/getAll() calls are free. */
    private static ?array $memo = null;

    public static function get(string $key, ?string $default = null): ?string
    {
        return static::all_()[$key] ?? $default;
    }

    public static function set(string $key, ?string $value): void
    {
        static::query()->updateOrCreate(['key' => $key], ['value' => $value]);

        static::flushCache();
    }

    public static function getAll(): array
    {
        return static::all_();
    }

    private static function all_(): array
    {
        if (static::$memo !== null) {
            return static::$memo;
        }

        try {
            return static::$memo = static::query()->pluck('value', 'key')->all();
        } catch (\Throwable $e) {
            return [];
        }
    }

    public static function flushCache(): void
    {
        static::$memo = null;
        try {
            Cache::forget(self::CACHE_KEY);
        } catch (\Throwable $e) {
        }
    }
}
