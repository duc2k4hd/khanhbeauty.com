<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

#[Fillable([
    'setting_key',
    'setting_value',
    'setting_group',
])]
class SiteSetting extends Model
{
    use HasFactory;

    /**
     * Disable timestamps if not needed, or keep for maintenance info.
     * Table migration only has updated_at.
     */
    const UPDATED_AT = 'updated_at';
    const CREATED_AT = null;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'updated_at' => 'datetime',
        ];
    }

    protected static $settingsCache = null;

    /**
     * Get a setting value by key, cached logic.
     */
    public static function getValue(string $key, $default = null)
    {
        if (self::$settingsCache === null) {
            try {
                self::$settingsCache = self::pluck('setting_value', 'setting_key')->toArray();
            } catch (\Exception $e) {
                // Return default if table doesn't exist yet
                self::$settingsCache = [];
            }
        }
        return self::$settingsCache[$key] ?? $default;
    }
}
