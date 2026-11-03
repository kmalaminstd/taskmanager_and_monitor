<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class SystemSetting extends Model
{
    use HasFactory;

    protected $fillable = [
        'key',
        'value',
        'type',
        'group',
    ];

    public static function get(string $key, $default = null)
    {
        return Cache::remember("setting_{$key}", 3600, function () use ($key, $default) {
            $setting = static::where('key', $key)->first();
            if (!$setting) {
                return $default;
            }

            return match ($setting->type) {
                'boolean' => filter_var($setting->value, FILTER_VALIDATE_BOOLEAN),
                'integer' => (int) $setting->value,
                'json'    => json_decode($setting->value, true),
                default   => $setting->value,
            };
        });
    }

    public static function set(string $key, $value, string $type = 'string', string $group = 'general'): void
    {
        $storeValue = is_array($value) ? json_encode($value) : (string) $value;

        static::updateOrCreate(
            ['key' => $key],
            ['value' => $storeValue, 'type' => $type, 'group' => $group]
        );

        Cache::forget("setting_{$key}");
    }
}
