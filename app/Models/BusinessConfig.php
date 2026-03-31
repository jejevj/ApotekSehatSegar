<?php

namespace App\Models;

use App\Models\Concerns\HasTenantScope;
use Illuminate\Database\Eloquent\Model;

class BusinessConfig extends Model
{
    use HasTenantScope;

    protected $table = 'business_configs';
    protected $fillable = ['store_id', 'key', 'value', 'type'];

    /**
     * Get a config value by key, cast to the appropriate type.
     */
    public static function getValue(string $key, mixed $default = null): mixed
    {
        $record = static::where('key', $key)->first();

        if (!$record) {
            return $default;
        }

        return match ($record->type) {
            'boolean' => filter_var($record->value, FILTER_VALIDATE_BOOLEAN),
            'integer' => (int) $record->value,
            'json'    => json_decode($record->value, true),
            default   => $record->value,
        };
    }

    /**
     * Set a config value by key, encoding arrays/objects as JSON.
     */
    public static function setValue(string $key, mixed $value): void
    {
        $stringValue = (is_array($value) || is_object($value))
            ? json_encode($value)
            : (string) $value;

        static::updateOrCreate(
            ['key' => $key],
            ['value' => $stringValue]
        );
    }
}
