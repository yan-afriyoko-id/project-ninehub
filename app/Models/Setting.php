<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Setting extends Model
{
    use HasFactory;

    protected $fillable = [
        'key',
        'value',
        'type',
        'group',
        'description',
        'is_public',
        'user_id',
    ];

    protected $casts = [
        'is_public' => 'boolean',
        'value' => 'json', // Allow complex values
    ];

    /**
     * Get the value with proper type casting
     */
    public function getTypedValueAttribute()
    {
        switch ($this->type) {
            case 'boolean':
                return filter_var($this->value, FILTER_VALIDATE_BOOLEAN);
            case 'integer':
                return (int) $this->value;
            case 'float':
                return (float) $this->value;
            case 'array':
            case 'json':
                return is_string($this->value) ? json_decode($this->value, true) : $this->value;
            default:
                return $this->value;
        }
    }

    /**
     * Set the value with proper type handling
     */
    public function setTypedValueAttribute($value)
    {
        if (is_array($value) || is_object($value)) {
            $this->attributes['value'] = json_encode($value);
            $this->attributes['type'] = 'json';
        } else {
            $this->attributes['value'] = (string) $value;
            $this->attributes['type'] = $this->type ?? 'string';
        }
    }

    /**
     * Scope for public settings
     */
    public function scopePublic($query)
    {
        return $query->where('is_public', true);
    }

    /**
     * Scope for private settings
     */
    public function scopePrivate($query)
    {
        return $query->where('is_public', false);
    }

    /**
     * Scope for settings by group
     */
    public function scopeByGroup($query, string $group)
    {
        return $query->where('group', $group);
    }

    /**
     * Scope for settings by type
     */
    public function scopeByType($query, string $type)
    {
        return $query->where('type', $type);
    }

    /**
     * Get setting value by key
     */
    public static function getValue(string $key, $default = null)
    {
        $setting = static::where('key', $key)->first();
        return $setting ? $setting->typed_value : $default;
    }

    /**
     * Set setting value by key
     */
    public static function setValue(string $key, $value, array $options = [])
    {
        $setting = static::where('key', $key)->first();

        if (!$setting) {
            $setting = new static(['key' => $key]);
        }

        $setting->typed_value = $value;
        $setting->fill($options);
        $setting->save();

        return $setting;
    }

    /**
     * Get multiple settings by keys
     */
    public static function getMultiple(array $keys, array $defaults = [])
    {
        $settings = static::whereIn('key', $keys)->get()->keyBy('key');

        $result = [];
        foreach ($keys as $key) {
            $result[$key] = $settings->get($key)?->typed_value ?? ($defaults[$key] ?? null);
        }

        return $result;
    }

    /**
     * Set multiple settings at once
     */
    public static function setMultiple(array $values, array $options = [])
    {
        foreach ($values as $key => $value) {
            static::setValue($key, $value, $options);
        }
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
