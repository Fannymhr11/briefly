<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ActivityHistory extends Model
{
    public $timestamps = false;

    public const ACTIONS = [
        'brief_uploaded' => 'Brief diupload',
        'brief_approved' => 'Brief disetujui',
        'brief_rejected' => 'Brief ditolak',
        'note_added' => 'Catatan diberikan',
        'task_in_progress' => 'Task diproses',
        'task_completed' => 'Task selesai',
        'task_pending' => 'Task direset',
        'user_created' => 'User ditambahkan',
        'user_updated' => 'User diubah',
        'user_deleted' => 'User dihapus',
    ];

    protected $fillable = ['user_id', 'brief_id', 'creative_task_id', 'action', 'description', 'created_at'];

    protected function casts(): array
    {
        return ['created_at' => 'datetime'];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function brief(): BelongsTo
    {
        return $this->belongsTo(Brief::class);
    }

    public function task(): BelongsTo
    {
        return $this->belongsTo(CreativeTask::class, 'creative_task_id');
    }

    /** Cakupan aktivitas yang boleh dilihat tiap role. */
    public function scopeVisibleTo(Builder $query, User $user): Builder
    {
        return match ($user->role) {
            'user' => $query->where(fn (Builder $w) => $w->where('user_id', $user->id)
                ->orWhereHas('brief', fn (Builder $b) => $b->where('user_id', $user->id))),
            'creative' => $query->where(fn (Builder $w) => $w->whereNotNull('creative_task_id')
                ->orWhere('action', 'brief_approved')->orWhere('user_id', $user->id)),
            'marketing' => $query->whereNotIn('action', ['user_created', 'user_updated', 'user_deleted']),
            default => $query,
        };
    }

    /** Catat aktivitas penting sistem. */
    public static function log(string $action, string $description, ?User $user = null, ?Brief $brief = null, ?CreativeTask $task = null): self
    {
        return self::create([
            'user_id' => $user?->id,
            'brief_id' => $brief?->id,
            'creative_task_id' => $task?->id,
            'action' => $action,
            'description' => $description,
            'created_at' => now(),
        ]);
    }

    /** Tone ikon/warna untuk timeline. */
    public function getToneAttribute(): string
    {
        return match ($this->action) {
            'brief_approved', 'task_completed' => 'green',
            'brief_rejected' => 'red',
            'note_added' => 'purple',
            'task_in_progress' => 'blue',
            'task_pending' => 'amber',
            'user_created', 'user_updated', 'user_deleted' => 'blue',
            default => 'blue',
        };
    }

    public function getIconAttribute(): string
    {
        return match ($this->action) {
            'brief_approved', 'task_completed' => 'check-circle',
            'brief_rejected' => 'x-circle',
            'note_added' => 'edit',
            'brief_uploaded' => 'upload',
            'task_in_progress', 'task_pending' => 'clock',
            default => 'user',
        };
    }
}
