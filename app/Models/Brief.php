<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Str;

class Brief extends Model
{
    public const STATUS_PENDING = 'pending';
    public const STATUS_APPROVED = 'approved';
    public const STATUS_REJECTED = 'rejected';

    public const STATUSES = [
        self::STATUS_PENDING => 'Menunggu Review',
        self::STATUS_APPROVED => 'Disetujui',
        self::STATUS_REJECTED => 'Ditolak',
    ];

    public const PLATFORMS = [
        'instagram_post' => 'Instagram Post',
        'instagram_reel' => 'Instagram Reel',
        'instagram_story' => 'Instagram Story',
        'tiktok' => 'TikTok',
    ];

    public const BRANDS = [
        'hurim' => 'Hurim',
        'dthree' => 'Dthree',
        'asfara' => 'Asfara',
    ];

    public const MAX_FILE_KB = 10240; // 10 MB

    protected $fillable = [
        'user_id', 'file_path', 'original_filename', 'file_size', 'platform', 'brand',
        'status', 'rejection_note', 'reviewed_by', 'reviewed_at', 'created_at', 'updated_at',
    ];

    protected function casts(): array
    {
        return ['reviewed_at' => 'datetime'];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function reviewer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }

    public function task(): HasOne
    {
        return $this->hasOne(CreativeTask::class);
    }

    public function histories(): HasMany
    {
        return $this->hasMany(ActivityHistory::class);
    }

    /* ---------- Accessors ---------- */

    /** Judul brief diturunkan dari nama file, mis. Brief_Campaign_Launch.pdf -> "Campaign Launch". */
    public function getTitleAttribute(): string
    {
        $name = pathinfo($this->original_filename, PATHINFO_FILENAME);
        $name = Str::of($name)->replace(['_', '-'], ' ')->squish();
        if ($name->lower()->startsWith('brief ') && $name->wordCount() > 1) {
            $name = $name->after(' ');
        }

        return (string) $name->title();
    }

    public function getPlatformLabelAttribute(): string
    {
        return self::PLATFORMS[$this->platform] ?? $this->platform;
    }

    public function getBrandLabelAttribute(): string
    {
        return self::BRANDS[$this->brand] ?? $this->brand;
    }

    public function getStatusLabelAttribute(): string
    {
        return self::STATUSES[$this->status] ?? $this->status;
    }

    public function getSizeLabelAttribute(): string
    {
        $mb = $this->file_size / 1048576;

        return $mb >= 0.1 ? number_format($mb, 1).' MB' : max(1, (int) round($this->file_size / 1024)).' KB';
    }

    /* ---------- Scopes ---------- */

    public function scopeStatus(Builder $q, string $status): Builder
    {
        return $q->where('status', $status);
    }

    /** Filter umum: q, status, brand, platform, from, to. */
    public function scopeFilter(Builder $query, array $f): Builder
    {
        return $query
            ->when($f['q'] ?? null, function (Builder $q, string $term) {
                $like = '%'.str_replace(['%', '_'], ['\%', '\_'], $term).'%';
                $q->where(function (Builder $w) use ($like) {
                    $w->where('original_filename', 'like', $like)
                        ->orWhere('brand', 'like', $like)
                        ->orWhere('platform', 'like', $like)
                        ->orWhereHas('user', fn (Builder $u) => $u->where('name', 'like', $like));
                });
            })
            ->when($f['status'] ?? null, fn (Builder $q, $v) => $q->where('status', $v))
            ->when($f['brand'] ?? null, fn (Builder $q, $v) => $q->where('brand', $v))
            ->when($f['platform'] ?? null, fn (Builder $q, $v) => $q->where('platform', $v))
            ->when($f['from'] ?? null, fn (Builder $q, $v) => $q->whereDate('created_at', '>=', $v))
            ->when($f['to'] ?? null, fn (Builder $q, $v) => $q->whereDate('created_at', '<=', $v));
    }
}
