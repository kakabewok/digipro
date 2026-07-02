<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

/**
 * @property int $id
 * @property string $key
 * @property string|null $value
 */
class WebsiteSetting extends Model
{
    protected $fillable = ['key', 'value'];

    /**
     * Get a setting value by key, with optional default.
     */
    public static function get(string $key, mixed $default = null): mixed
    {
        return Cache::remember("website_setting.{$key}", 3600, function () use ($key, $default) {
            $setting = static::where('key', $key)->first();

            return $setting ? $setting->value : $default;
        });
    }

    /**
     * Set a setting value by key.
     */
    public static function set(string $key, mixed $value): void
    {
        static::updateOrCreate(
            ['key' => $key],
            ['value' => $value],
        );

        Cache::forget("website_setting.{$key}");
    }

    /**
     * Get all settings as a key-value array.
     */
    public static function allSettings(): array
    {
        return Cache::remember('website_settings.all', 3600, function () {
            return static::pluck('value', 'key')->toArray();
        });
    }

    /**
     * Clear settings cache on save or delete.
     */
    protected static function booted(): void
    {
        static::saved(function () {
            Cache::forget('website_settings.all');
        });

        static::deleted(function () {
            Cache::forget('website_settings.all');
        });
    }
}
