<?php

namespace App\Http\Controllers;

use App\Models\Brief;
use App\Support\Stats;
use Illuminate\Http\Request;
use Illuminate\View\View;

/** Daftar & detail brief untuk Admin dan Marketing Communication. */
class BriefController extends Controller
{
    public function index(Request $request): View
    {
        $f = Stats::filters($request);

        return view('briefs.index', [
            'filters' => $f,
            'briefs' => Brief::with('user')->filter($f)->latest()->paginate(10)->withQueryString(),
            'stats' => [
                'total' => [Brief::count(), \App\Support\Stats::delta(Brief::query())],
                'pending' => [Brief::status('pending')->count(), Stats::delta(Brief::status('pending'))],
                'approved' => [Brief::status('approved')->count(), Stats::delta(Brief::status('approved'))],
                'rejected' => [Brief::status('rejected')->count(), Stats::delta(Brief::status('rejected'))],
            ],
        ]);
    }

    public function show(Brief $brief): View
    {
        $brief->load(['user', 'reviewer', 'task.assignee']);

        return view('briefs.show', [
            'brief' => $brief,
            'histories' => $brief->histories()->with('user')->latest('created_at')->latest('id')->get(),
        ]);
    }
}
