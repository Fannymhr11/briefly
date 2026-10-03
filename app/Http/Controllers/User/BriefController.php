<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreBriefRequest;
use App\Models\ActivityHistory;
use App\Models\Brief;
use App\Support\Stats;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;

class BriefController extends Controller
{
    public function create(): View
    {
        return view('user.briefs.create');
    }

    public function store(StoreBriefRequest $request): RedirectResponse
    {
        $file = $request->file('file');
        $data = $request->validated();

        // Nama file di storage di-random (aman); nama asli dibersihkan untuk tampilan.
        $path = $file->storeAs('briefs', Str::uuid()->toString().'.pdf', 'local');
        $original = preg_replace('/[^\p{L}\p{N}\s._()-]/u', '', $file->getClientOriginalName());
        $original = Str::limit(trim($original) ?: 'brief.pdf', 140, '');

        $brief = Brief::create([
            'user_id' => $request->user()->id,
            'file_path' => $path,
            'original_filename' => $original,
            'file_size' => $file->getSize(),
            'platform' => $data['platform'],
            'brand' => $data['brand'],
            'status' => Brief::STATUS_PENDING,
        ]);

        ActivityHistory::log(
            'brief_uploaded',
            sprintf('Brief baru "%s" diupload oleh %s', $brief->title, $request->user()->name),
            $request->user(),
            $brief
        );

        return redirect()->route('user.briefs.index')
            ->with('success', 'Brief berhasil dikirim. Status: Menunggu Review.');
    }

    public function index(Request $request): View
    {
        $f = Stats::filters($request);
        $mine = fn () => Brief::query()->where('user_id', $request->user()->id);

        return view('user.briefs.index', [
            'filters' => $f,
            'briefs' => $mine()->filter($f)->latest()->paginate(10)->withQueryString(),
            'total' => $mine()->count(),
        ]);
    }

    public function show(Request $request, Brief $brief): View
    {
        abort_unless($brief->user_id === $request->user()->id, 403);
        $brief->load(['user', 'reviewer', 'task.assignee']);

        return view('briefs.show', [
            'brief' => $brief,
            'histories' => $brief->histories()->with('user')->latest('created_at')->latest('id')->get(),
        ]);
    }
}
