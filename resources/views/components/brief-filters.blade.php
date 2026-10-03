@props(['action', 'filters' => [], 'status' => true, 'dates' => true, 'placeholder' => 'Cari brief…', 'statusOptions' => null])
<form method="GET" action="{{ $action }}" {{ $attributes->class(['flex flex-wrap items-center gap-2.5']) }}>
    <div class="search-wrap min-w-[200px] flex-1 lg:max-w-xs">
        <x-icon name="search" />
        <input type="search" name="q" value="{{ $filters['q'] ?? '' }}" placeholder="{{ $placeholder }}" class="input" aria-label="Cari">
    </div>
    <div class="flex flex-1 flex-wrap items-center justify-end gap-2.5">
        <select name="platform" class="select w-full sm:w-auto" onchange="this.form.submit()" aria-label="Filter platform">
            <option value="">Semua Platform</option>
            @foreach (\App\Models\Brief::PLATFORMS as $k => $l)<option value="{{ $k }}" @selected(($filters['platform'] ?? '') === $k)>{{ $l }}</option>@endforeach
        </select>
        <select name="brand" class="select w-full sm:w-auto" onchange="this.form.submit()" aria-label="Filter brand">
            <option value="">Semua Brand</option>
            @foreach (\App\Models\Brief::BRANDS as $k => $l)<option value="{{ $k }}" @selected(($filters['brand'] ?? '') === $k)>{{ $l }}</option>@endforeach
        </select>
        @if ($status)
            <select name="status" class="select w-full sm:w-auto" onchange="this.form.submit()" aria-label="Filter status">
                @foreach (($statusOptions ?? ['' => 'Semua Status'] + \App\Models\Brief::STATUSES) as $k => $l)<option value="{{ $k }}" @selected((string) ($filters['status'] ?? '') === (string) $k)>{{ $l }}</option>@endforeach
            </select>
        @endif
        @if ($dates)<x-date-range :from="$filters['from'] ?? null" :to="$filters['to'] ?? null" />@endif
        @if (collect($filters)->filter()->isNotEmpty())
            <a href="{{ $action }}" class="btn btn-outline" title="Reset filter"><x-icon name="x" class="h-4 w-4" />Reset</a>
        @endif
    </div>
</form>
