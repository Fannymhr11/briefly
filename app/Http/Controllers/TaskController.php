<?php

namespace App\Http\Controllers;

use App\Http\Requests\TaskStatusRequest;
use App\Models\ActivityHistory;
use App\Models\CreativeTask;
use App\Support\Stats;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

/** Creative Tasks — Tim Kreatif (ubah status), Admin & Marketing (pantau saja). */
class TaskController extends Controller
{
    public function index(Request $request, ?CreativeTask $task = null): View
    {
        $f = Stats::filters($request);
        $count = fn (?string $s = null) => CreativeTask::query()->when($s, fn ($q) => $q->where('status', $s))->count();

        if ($task) {
            $task->load(['brief.user', 'assignee']);
        }

        return view('tasks.index', [
            'filters' => $f,
            'tasks' => CreativeTask::with(['brief.user', 'assignee'])->filter($f)->latest()->paginate(10)->withQueryString(),
            'selected' => $task,
            'canUpdate' => $request->user()->hasRole('creative'),
            'counts' => [
                'total' => [$count(), Stats::delta(CreativeTask::query())],
                'pending' => [$count('pending'), Stats::delta(CreativeTask::where('status', 'pending'))],
                'in_progress' => [$count('in_progress'), Stats::delta(CreativeTask::where('status', 'in_progress'))],
                'completed' => [$count('completed'), Stats::delta(CreativeTask::where('status', 'completed'))],
            ],
            'taskHistories' => $task
                ? ActivityHistory::with('user')->where('brief_id', $task->brief_id)->latest('created_at')->latest('id')->limit(4)->get()
                : collect(),
        ]);
    }

    public function updateStatus(TaskStatusRequest $request, CreativeTask $task): RedirectResponse
    {
        $new = $request->validated()['status'];

        if ($new === $task->status) {
            return back()->with('toast_info', 'Status task tidak berubah.');
        }

        $data = ['status' => $new, 'assigned_to' => $task->assigned_to ?? $request->user()->id];

        if ($new === CreativeTask::STATUS_IN_PROGRESS) {
            $data += ['started_at' => $task->started_at ?? now(), 'completed_at' => null];
        } elseif ($new === CreativeTask::STATUS_COMPLETED) {
            $data += ['started_at' => $task->started_at ?? now(), 'completed_at' => now()];
        } else {
            $data += ['started_at' => null, 'completed_at' => null];
        }

        $task->update($data);

        $action = ['in_progress' => 'task_in_progress', 'completed' => 'task_completed', 'pending' => 'task_pending'][$new];
        $label = CreativeTask::STATUSES[$new];
        $title = $task->brief->title;

        ActivityHistory::log(
            $action,
            $new === 'completed'
                ? sprintf('Task "%s" ditandai Selesai oleh %s', $title, $request->user()->name)
                : sprintf('Task "%s" dipindahkan ke %s oleh %s', $title, $label, $request->user()->name),
            $request->user(), $task->brief, $task
        );

        return redirect()->route('creative.tasks.show', $task)
            ->with('success', 'Status task diubah menjadi '.$label.'.');
    }
}
