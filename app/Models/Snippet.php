<?php

namespace App\Models;

use Database\Factories\SnippetFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['alias', 'title', 'content', 'content_type', 'language', 'is_public', 'password', 'expires_at'])]
#[Hidden(['password', 'owner_token'])]
class Snippet extends Model
{
    /** @use HasFactory<SnippetFactory> */
    use HasFactory;

    protected static function boot(): void
    {
        parent::boot();

        static::creating(function (Snippet $snippet): void {
            if (is_null($snippet->user_id)) {
                $snippet->is_public = true;
            }
        });

        static::updating(function (Snippet $snippet): void {
            if (is_null($snippet->user_id)) {
                $snippet->is_public = true;
            }
        });
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    protected function casts(): array
    {
        return [
            'is_public' => 'boolean',
            'views_count' => 'integer',
            'expires_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function isExpired(): bool
    {
        return $this->expires_at !== null && $this->expires_at->isPast();
    }

    public function isAnonymous(): bool
    {
        return is_null($this->user_id);
    }

    public function isProtected(): bool
    {
        return filled($this->password);
    }

    public function verifyPassword(string $password): bool
    {
        return ! $this->isProtected() || \Hash::check($password, $this->password);
    }
}
