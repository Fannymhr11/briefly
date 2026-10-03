<?php

namespace App\Http\Controllers;

use App\Models\ActivityHistory;
use App\Models\Brief;
use App\Models\CreativeTask;
use App\Models\User;
use App\Support\Stats;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\View\View;

class HistoryController extends Controller
{
    public function index(Request $request): View
    {
        $user = $request->user();
        $f = Stats::filters($request);
        $systemActions = ['user_created', 'user_updated', 'user_deleted'];

        $query = ActivityHistory::with(['user', 'brief', 'task']);

        // Cakupan history sesuai role.
        $query->visibleTo($user);

        $query
            ->when($f['q'] ?? null, fn ($q, $t) => $q->where('description', 'like', '%'.str_replace(['%', '_'], ['\%', '\_'], $t).'%'))
            ->when($f['action'] ?? null, fn ($q, $v) => $q->where('action', $v))
            ->when($f['user'] ?? null, fn ($q, $v) => $q->where('user_id', $v))
            ->when($f['platform'] ?? null, fn ($q, $v) => $q->whereHas('brief', fn ($b) => $b->where('platform', $v)))
            ->when($f['brand'] ?? null, fn ($q, $v) => $q->whereHas('brief', fn ($b) => $b->where('brand', $v)))
            ->when($f['status'] ?? null, fn ($q, $v) => $q->whereHas('brief', fn ($b) => $b->where('status', $v)))
            ->when($f['from'] ?? null, fn ($q, $v) => $q->whereDate('created_at', '>=', $v))
            ->when($f['to'] ?? null, fn ($q, $v) => $q->whereDate('created_at', '<=', $v));

        $actions = collect(ActivityHistory::ACTIONS)
            ->when($user->role !== 'admin', fn ($c) => $c->except($systemActions));

        // Kartu ringkasan sesuai role.
        if ($user->role === 'creative') {
            $summary = [
                ['Total Task', CreativeTask::count(), 'file'],
                ['Belum Dikerjakan', CreativeTask::where('status', 'pending')->count(), 'clock-amber'],
                ['Sedang Diproses', CreativeTask::where('status', 'in_progress')->count(), 'loader'],
                ['Sudah Selesai', CreativeTask::where('status', 'completed')->count(), 'check-green'],
            ];
        } else {
            $b = fn () => Brief::query()->when($user->role === 'user', fn ($q) => $q->where('user_id', $user->id));
            $summary = [
                ['Total Brief', $b()->count(), 'file'],
                ['Menunggu Review', $b()->status('pending')->count(), 'clock-amber'],
                ['Disetujui', $b()->status('approved')->count(), 'check-green'],
                ['Ditolak', $b()->status('rejected')->count(), 'x-red'],
            ];
        }

        return view('history.index', [
            'filters' => $f,
            'histories' => $query->latest('created_at')->latest('id')->paginate(8)->withQueryString(),
            'actions' => $actions,
            'summary' => $summary,
            'users' => in_array($user->role, ['admin', 'marketing'], true) ? User::orderBy('name')->get(['id', 'name']) : collect(),
        ]);
    }
}
