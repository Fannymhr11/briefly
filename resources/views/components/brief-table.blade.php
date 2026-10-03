{{-- action: icons (lihat + menu) | review (tombol Review/Lihat) | detail (tombol Detail) | none --}}
@props(['briefs', 'action' => 'icons', 'sender' => true, 'note' => false, 'fileSub' => true, 'empty' => 'Belum ada brief.'])
@php
$paginated = $briefs instanceof \Illuminate\Contracts\Pagination\LengthAwarePaginator;
$rows = $paginated ? $briefs->getCollection() : collect($briefs);
$start = $paginated ? ($briefs->firstItem() ?? 1) - 1 : 0;
$role = auth()->user()->role;
@endphp
@if ($rows->isEmpty())
    <x-empty-state :title="$empty" description="Data akan muncul di sini setelah ada brief yang sesuai." icon="file-text" />
@else
<x-table>
    <x-slot:head>
        <th class="w-12">No</th>
        <th>Nama Brief</th>
        <th>Platform</th>
        <th>Brand</th>
        @if ($sender)<th>Pengirim</th>@endif
        <th>Tanggal Upload</th>
        <th>Status</th>
        @if ($note)<th>Catatan MC</th>@endif
        @if ($action !== 'none')<th class="text-right">Aksi</th>@endif
    </x-slot:head>
    @foreach ($rows as $i => $b)
        @php $show = route($role.'.briefs.show', $b); @endphp
        <tr>
            <td class="text-muted">{{ $start + $i + 1 }}</td>
            <td class="min-w-[190px]">
                <a href="{{ $show }}" class="font-medium text-ink hover:text-brand-600 dark:hover:text-brand-300">{{ $b->title }}</a>
                @if ($fileSub)<p class="mt-0.5 max-w-[240px] truncate text-xs text-muted">{{ $b->original_filename }} · {{ $b->size_label }}</p>@endif
            </td>
            <td><x-platform-icon :platform="$b->platform" :label="true" /></td>
            <td><x-brand-avatar :brand="$b->brand" :label="true" /></td>
            @if ($sender)
                <td><p class="whitespace-nowrap font-medium">{{ $b->user->name ?? '-' }}</p><p class="text-xs text-muted">{{ $b->user?->role_label }}</p></td>
            @endif
            <td class="whitespace-nowrap text-ink/80">{{ $b->created_at->translatedFormat('j M Y') }}<span class="block text-xs text-muted">{{ $b->created_at->format('H:i') }}</span></td>
            <td><x-status-badge :status="$b->status" /></td>
            @if ($note)
                <td class="max-w-[220px] text-[13px] text-ink/80">{{ $b->rejection_note ?: '—' }}</td>
            @endif
            @if ($action !== 'none')
                <td class="text-right">
                    <div class="inline-flex items-center justify-end gap-1.5">
                        @if ($action === 'review')
                            @if ($b->status === 'pending')
                                <x-button size="sm" :href="$show">Review</x-button>
                            @else
                                <x-button size="sm" variant="outline" :href="$show">Lihat</x-button>
                            @endif
                        @elseif ($action === 'detail')
                            <x-button size="sm" variant="soft" :href="$show">Detail</x-button>
                        @else
                            <a href="{{ $show }}" class="btn-icon-soft" title="Lihat detail" aria-label="Lihat detail {{ $b->title }}"><x-icon name="eye" class="h-4 w-4" /></a>
                        @endif
                        <x-dropdown width="w-48">
                            <x-slot:trigger><button type="button" class="btn-icon" aria-label="Menu aksi"><x-icon name="more" class="h-4 w-4" /></button></x-slot:trigger>
                            <a href="{{ $show }}" class="dd-item"><x-icon name="eye" class="h-4 w-4 text-muted" />Detail</a>
                            <a href="{{ route('briefs.preview', $b) }}" target="_blank" rel="noopener" class="dd-item"><x-icon name="file-text" class="h-4 w-4 text-muted" />Preview PDF</a>
                            <a href="{{ route('briefs.download', $b) }}" class="dd-item"><x-icon name="download" class="h-4 w-4 text-muted" />Download</a>
                        </x-dropdown>
                    </div>
                </td>
            @endif
        </tr>
    @endforeach
</x-table>
@endif
