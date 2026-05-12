<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Category extends Model
{
    protected $fillable = [
        'user_id',
        'name',
        'type',
        'kind',
        'color',
    ];

    /**
     * @return array<int, string>
     */
    public static function typeKeys(): array
    {
        return array_keys(config('finanzas.category_types', []));
    }

    /**
     * @return array<string, string>
     */
    public static function typeLabels(): array
    {
        return config('finanzas.category_types', []);
    }

    /**
     * @return array<int, string>
     */
    public static function kindKeysForType(?string $type): array
    {
        if (! $type) {
            return [];
        }

        return array_keys(config('finanzas.category_kinds.'.$type, []));
    }

    public function typeLabel(): string
    {
        return config('finanzas.category_types.'.$this->type, $this->type);
    }

    public function kindLabel(): ?string
    {
        if (! $this->kind) {
            return null;
        }

        return config('finanzas.category_kinds.'.$this->type.'.'.$this->kind) ?? $this->kind;
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
