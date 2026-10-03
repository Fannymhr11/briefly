@props(['tasks', 'assignee' => false, 'selectedId' => null, 'compact' => false])
@php
$paginated = $tasks instanceof \Illuminate\Contracts\Pagination\LengthAwarePaginator;
$rows = $paginated ? $tasks->getCollection() : collect($tasks);
$start = $paginated ? ($tasks->firstItem() ?? 1) - 1 : 0;
$role = auth()->user()->role;
@endphp
@if ($rows->isEmpty())
    <x-empty-state title="Belum ada creative task" description="Task muncul otomatis setelah Marketing Communication menyetujui brief." icon="check-square" />
@else
<x-table>
    <x-slot:head>
        <th class="w-12">No</th>
        <th>Nama Brief</th>
        <th>Brand</th>
        <th>Platform</th>
        <th>Pengirim</th>
        @if ($assignee)<th>Assigned To</th>@endif
        <th>Approved</th>
        <th>Status</th>
        <th class="text-right">Aksi</th>
    </x-slot:head>
    @foreach ($rows as $i => $t)
        @php $b = $t->brief; $url = route($role.'.tasks.show', $t); @endphp
        <tr @class(['bg-brand-500/[0.06]' => $selectedId === $t->id])>
            <td class="text-muted">{{ $start + $i + 1 }}</td>
            <td class="min-w-[180px]"><a href="{{ $url }}" class="font-medium hover:text-brand-600 dark:hover:text-brand-300">{{ $b->title }}</a></td>
            <td><x-brand-avatar :brand="$b->brand" :label="true" /></td>
            <td><x-platform-icon :platform="$b->platform" :label="true" /></td>
            <td class="whitespace-nowrap">{{ $b->user->name ?? '-' }}</td>
            @if ($assignee)<td class="whitespace-nowrap text-ink/80">{{ $t->assignee->name ?? '—' }}</td>@endif
            <td class="whitespace-nowrap text-ink/80">{{ $t->created_at->translatedFormat('j M Y') }}</td>
            <td><x-status-badge :status="$t->status" type="task" /></td>
            <td class="text-right"><a href="{{ $url }}" class="btn-icon ml-auto" aria-label="Detail task {{ $b->title }}"><x-icon name="chevron-right" class="h-4 w-4" /></a></td>
        </tr>
    @endforeach
</x-table>
@endif
