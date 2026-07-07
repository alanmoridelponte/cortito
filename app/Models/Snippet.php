<?php

namespace App\Models;

use App\Support\OwnerToken;
use Database\Factories\SnippetFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Http\Request;

#[Fillable(['alias', 'title', 'content', 'content_type', 'language', 'is_public', 'password', 'expires_at', 'is_edited', 'edited_at', 'owner_token', 'ip_address'])]
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
            'is_edited' => 'boolean',
            'views_count' => 'integer',
            'expires_at' => 'datetime',
            'edited_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function isExpired(): bool
    {
        return $this->expires_at !== null && $this->expires_at->isPast();
    }

    public function scopeActive($query): void
    {
        $query->where(function ($q) {
            $q->whereNull('expires_at')
                ->orWhere('expires_at', '>', now());
        });
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

    public function markAsEdited(): void
    {
        $this->update([
            'is_edited' => true,
            'edited_at' => now(),
        ]);
    }

    public function canBeEditedBy(Request $request): bool
    {
        if ($request->user()) {
            return $this->user_id === $request->user()->id;
        }

        $hash = OwnerToken::getHashFromRequest($request);

        return $hash && $this->owner_token === $hash;
    }
}
