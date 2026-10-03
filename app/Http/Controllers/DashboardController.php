<?php

namespace App\Http\Controllers;

use App\Models\ActivityHistory;
use App\Models\Brief;
use App\Models\CreativeTask;
use App\Models\User;
use App\Support\Stats;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    /** Segmen donut status brief. */
    private function briefSegments(?int $userId = null, bool $withPending = true): array
    {
        $base = fn () => Brief::query()->when($userId, fn ($q) => $q->where('user_id', $userId));

        $segments = [];
        if ($withPending) {
            $segments[] = ['label' => 'Menunggu Review', 'value' => $base()->status('pending')->count(), 'color' => '#FBBF24'];
        }
        array_unshift($segments, ['label' => 'Disetujui', 'value' => $base()->status('approved')->count(), 'color' => '#3B82F6']);
        $segments[] = ['label' => 'Ditolak', 'value' => $base()->status('rejected')->count(), 'color' => '#F43F5E'];

        return $segments;
    }

    public function admin(): View
    {
        $statusCount = fn (string $s) => Brief::query()->status($s);

        return view('dashboards.admin', [
            'stats' => [
                'users' => [User::count(), Stats::delta(User::query())],
                'briefs' => [Brief::count(), Stats::delta(Brief::query())],
                'pending' => [$statusCount('pending')->count(), Stats::delta($statusCount('pending'))],
                'approved' => [$statusCount('approved')->count(), Stats::delta($statusCount('approved'))],
                'rejected' => [$statusCount('rejected')->count(), Stats::delta($statusCount('rejected'))],
                'tasks' => [CreativeTask::count(), CreativeTask::where('status', 'completed')->count()],
            ],
            'trend' => Stats::trend(Brief::query()),
            'segments' => $this->briefSegments(),
            'total' => Brief::count(),
            'recentBriefs' => Brief::with('user')->latest()->limit(5)->get(),
            'activities' => ActivityHistory::with(['user', 'brief'])->latest('created_at')->latest('id')->limit(5)->get(),
        ]);
    }

    public function marketing(): View
    {
        $statusCount = fn (string $s) => Brief::query()->status($s);

        return view('dashboards.marketing', [
            'stats' => [
                'briefs' => [Brief::count(), Stats::delta(Brief::query())],
                'pending' => [$statusCount('pending')->count(), Stats::delta($statusCount('pending'))],
                'approved' => [$statusCount('approved')->count(), Stats::delta($statusCount('approved'))],
                'rejected' => [$statusCount('rejected')->count(), Stats::delta($statusCount('rejected'))],
                'tasks' => [CreativeTask::count(), Stats::delta(CreativeTask::query())],
            ],
            'trend' => Stats::trend(Brief::query()),
            'segments' => $this->briefSegments(),
            'total' => Brief::count(),
            'waiting' => Brief::with('user')->status('pending')->oldest()->limit(5)->get(),
            'recentBriefs' => Brief::with('user')->latest()->limit(5)->get(),
            'activities' => ActivityHistory::with(['user', 'brief'])
                ->whereNotIn('action', ['user_created', 'user_updated', 'user_deleted'])
                ->latest('created_at')->latest('id')->limit(5)->get(),
        ]);
    }

    public function user(Request $request): View
    {
        $uid = $request->user()->id;
        $mine = fn () => Brief::query()->where('user_id', $uid);

        return view('dashboards.user', [
            'stats' => [
                'briefs' => [$mine()->count(), Stats::delta($mine())],
                'pending' => [$mine()->status('pending')->count(), Stats::delta($mine()->status('pending'))],
                'approved' => [$mine()->status('approved')->count(), Stats::delta($mine()->status('approved'))],
                'rejected' => [$mine()->status('rejected')->count(), Stats::delta($mine()->status('rejected'))],
            ],
            'trend' => Stats::trend($mine()),
            'segments' => $this->briefSegments($uid),
            'total' => $mine()->count(),
            'recentBriefs' => $mine()->latest()->limit(5)->get(),
        ]);
    }

    public function creative(Request $request): View
    {
        $f = Stats::filters($request);
        $count = fn (?string $s = null) => CreativeTask::query()->when($s, fn ($q) => $q->where('status', $s))->count();
        $done = $count('completed');
        $total = $count();

        return view('dashboards.creative', [
            'filters' => $f,
            'counts' => [
                'total' => $total,
                'pending' => $count('pending'),
                'in_progress' => $count('in_progress'),
                'completed' => $done,
            ],
            'percent' => $total ? (int) round($done / $total * 100) : 0,
            'segments' => [
                ['label' => 'Sudah Selesai', 'value' => $done, 'color' => '#10B981'],
                ['label' => 'Sedang Diproses', 'value' => $count('in_progress'), 'color' => '#3B82F6'],
                ['label' => 'Belum Dikerjakan', 'value' => $count('pending'), 'color' => '#FBBF24'],
            ],
            'recentTasks' => CreativeTask::with('brief.user')->latest()->limit(4)->get(),
            'tasks' => CreativeTask::with('brief.user')->filter($f)->latest()->paginate(5)->withQueryString(),
            'activities' => ActivityHistory::with(['user', 'brief'])
                ->where(fn ($w) => $w->whereNotNull('creative_task_id')->orWhere('action', 'brief_approved'))
                ->latest('created_at')->latest('id')->limit(4)->get(),
        ]);
    }
}
