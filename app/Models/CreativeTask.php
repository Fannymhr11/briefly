<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CreativeTask extends Model
{
    public const STATUS_PENDING = 'pending';
    public const STATUS_IN_PROGRESS = 'in_progress';
    public const STATUS_COMPLETED = 'completed';

    public const STATUSES = [
        self::STATUS_PENDING => 'Belum Dikerjakan',
        self::STATUS_IN_PROGRESS => 'Sedang Diproses',
        self::STATUS_COMPLETED => 'Sudah Selesai',
    ];

    protected $fillable = ['brief_id', 'assigned_to', 'status', 'started_at', 'completed_at', 'created_at', 'updated_at'];

    protected function casts(): array
    {
        return ['started_at' => 'datetime', 'completed_at' => 'datetime'];
    }

    public function brief(): BelongsTo
    {
        return $this->belongsTo(Brief::class);
    }

    public function assignee(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function histories(): HasMany
    {
        return $this->hasMany(ActivityHistory::class);
    }

    public function getStatusLabelAttribute(): string
    {
        return self::STATUSES[$this->status] ?? $this->status;
    }

    /** Filter: q, status, brand, platform, from, to (tanggal approved). */
    public function scopeFilter(Builder $query, array $f): Builder
    {
        return $query
            ->when($f['q'] ?? null, function (Builder $q, string $term) {
                $like = '%'.str_replace(['%', '_'], ['\%', '\_'], $term).'%';
                $q->whereHas('brief', function (Builder $b) use ($like) {
                    $b->where('original_filename', 'like', $like)
                        ->orWhere('brand', 'like', $like)
                        ->orWhere('platform', 'like', $like)
                        ->orWhereHas('user', fn (Builder $u) => $u->where('name', 'like', $like));
                });
            })
            ->when($f['status'] ?? null, fn (Builder $q, $v) => $q->where('status', $v))
            ->when($f['brand'] ?? null, fn (Builder $q, $v) => $q->whereHas('brief', fn (Builder $b) => $b->where('brand', $v)))
            ->when($f['platform'] ?? null, fn (Builder $q, $v) => $q->whereHas('brief', fn (Builder $b) => $b->where('platform', $v)))
            ->when($f['from'] ?? null, fn (Builder $q, $v) => $q->whereDate('created_at', '>=', $v))
            ->when($f['to'] ?? null, fn (Builder $q, $v) => $q->whereDate('created_at', '<=', $v));
    }
}
