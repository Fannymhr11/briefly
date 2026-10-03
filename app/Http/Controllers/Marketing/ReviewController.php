<?php

namespace App\Http\Controllers\Marketing;

use App\Http\Controllers\Controller;
use App\Http\Requests\RejectBriefRequest;
use App\Models\ActivityHistory;
use App\Models\Brief;
use App\Models\CreativeTask;
use App\Support\Stats;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\View\View;

class ReviewController extends Controller
{
    /** Antrian "Review Brief" — default hanya brief menunggu review. */
    public function index(Request $request): View
    {
        $f = Stats::filters($request);
        $status = $f['status'] ?? 'pending';
        $query = Brief::with('user')->filter(array_merge($f, ['status' => $status === 'all' ? null : $status]));

        $count = fn (string $col, string $val) => Brief::where($col, $val)->count();

        return view('briefs.review', [
            'filters' => array_merge($f, ['status' => $status]),
            'briefs' => $query->oldest()->paginate(8)->withQueryString(),
            'stats' => [
                'total' => [Brief::count(), Stats::delta(Brief::query())],
                'pending' => [$count('status', 'pending'), Stats::delta(Brief::status('pending'))],
                'approved' => [$count('status', 'approved'), Stats::delta(Brief::status('approved'))],
                'rejected' => [$count('status', 'rejected'), Stats::delta(Brief::status('rejected'))],
            ],
            'byStatus' => collect(Brief::STATUSES)->map(fn ($l, $k) => $count('status', $k)),
            'byPlatform' => collect(Brief::PLATFORMS)->map(fn ($l, $k) => $count('platform', $k)),
            'byBrand' => collect(Brief::BRANDS)->map(fn ($l, $k) => $count('brand', $k)),
        ]);
    }

    public function approve(Request $request, Brief $brief): RedirectResponse
    {
        if ($brief->status !== Brief::STATUS_PENDING) {
            return back()->with('toast_error', 'Brief ini sudah direview sebelumnya.');
        }

        DB::transaction(function () use ($request, $brief) {
            $brief->update([
                'status' => Brief::STATUS_APPROVED,
                'rejection_note' => null,
                'reviewed_by' => $request->user()->id,
                'reviewed_at' => now(),
            ]);

            // Otomatis membuat Creative Task untuk Tim Kreatif.
            $task = CreativeTask::firstOrCreate(
                ['brief_id' => $brief->id],
                ['status' => CreativeTask::STATUS_PENDING]
            );

            ActivityHistory::log(
                'brief_approved',
                sprintf('Brief "%s" disetujui oleh %s dan masuk ke task kreatif', $brief->title, $request->user()->name),
                $request->user(), $brief, $task
            );
        });

        return redirect()->route('marketing.review')
            ->with('success', 'Brief disetujui dan otomatis masuk ke task Tim Kreatif.');
    }

    public function reject(RejectBriefRequest $request, Brief $brief): RedirectResponse
    {
        if ($brief->status !== Brief::STATUS_PENDING) {
            return back()->with('toast_error', 'Brief ini sudah direview sebelumnya.');
        }

        $note = trim($request->validated()['rejection_note']);

        DB::transaction(function () use ($request, $brief, $note) {
            $brief->update([
                'status' => Brief::STATUS_REJECTED,
                'rejection_note' => $note,
                'reviewed_by' => $request->user()->id,
                'reviewed_at' => now(),
            ]);

            ActivityHistory::log(
                'brief_rejected',
                sprintf('Brief "%s" ditolak oleh %s', $brief->title, $request->user()->name),
                $request->user(), $brief
            );
            ActivityHistory::log(
                'note_added',
                sprintf('Catatan untuk "%s": %s', $brief->title, Str::limit($note, 160)),
                $request->user(), $brief
            );
        });

        return redirect()->route('marketing.review')
            ->with('success', 'Brief ditolak dan catatan telah dikirim ke User.');
    }
}
