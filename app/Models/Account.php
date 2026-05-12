<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Account extends Model
{
    protected $fillable = [
        'user_id',
        'name',
        'type',
        'currency',
        'notes',
    ];

    /**
     * @return array<int, string>
     */
    public static function typeKeys(): array
    {
        return array_keys(config('finanzas.account_types', []));
    }

    /**
     * @return array<string, string>
     */
    public static function typeLabels(): array
    {
        return config('finanzas.account_types', []);
    }

    public function typeLabel(): string
    {
        return config('finanzas.account_types.'.$this->type, $this->type);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function transactions(): HasMany
    {
        return $this->hasMany(Transaction::class);
    }
}
