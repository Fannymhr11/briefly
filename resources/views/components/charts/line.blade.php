@props(['labels' => [], 'values' => [], 'unit' => 'brief'])
@php
$W = 640; $H = 250; $L = 34; $R = 16; $T = 34; $B = 30;
$n = max(count($values), 1);
$max = max(array_merge([1], $values));
$step = max(1, (int) ceil($max / 4));
$top = $step * 4;
$plotW = $W - $L - $R; $plotH = $H - $T - $B;
$pts = [];
foreach ($values as $i => $v) {
    $pts[] = [$L + ($n > 1 ? $i * $plotW / ($n - 1) : $plotW / 2), $T + $plotH * (1 - $v / $top)];
}
$f = fn ($x) => round($x, 1);
$line = '';
foreach ($pts as $i => [$x, $y]) {
    if ($i === 0) { $line .= 'M'.$f($x).' '.$f($y); continue; }
    [$px, $py] = $pts[$i - 1]; $mx = ($px + $x) / 2;
    $line .= ' C'.$f($mx).' '.$f($py).' '.$f($mx).' '.$f($y).' '.$f($x).' '.$f($y);
}
$baseY = $T + $plotH;
$area = count($pts) ? $line.' L'.$f(end($pts)[0]).' '.$baseY.' L'.$f($pts[0][0]).' '.$baseY.' Z' : '';
$gid = 'lg'.substr(md5(json_encode($values).rand()), 0, 6);
$last = count($pts) ? end($pts) : [0, 0];
$bx = min(max($last[0] - 34, $L), $W - $R - 68);
@endphp
<svg viewBox="0 0 {{ $W }} {{ $H }}" class="h-auto w-full" role="img" aria-label="Grafik tren {{ $unit }} 7 hari terakhir">
    <defs>
        <linearGradient id="{{ $gid }}" x1="0" y1="0" x2="0" y2="1">
            <stop offset="0" stop-color="#3B7BF6" stop-opacity=".22"/><stop offset="1" stop-color="#3B7BF6" stop-opacity="0"/>
        </linearGradient>
    </defs>
    @for ($i = 0; $i <= 4; $i++)
        @php $y = $T + $plotH * (1 - $i / 4); @endphp
        <line x1="{{ $L }}" x2="{{ $W - $R }}" y1="{{ $f($y) }}" y2="{{ $f($y) }}" class="stroke-line" stroke-width="1"/>
        <text x="{{ $L - 8 }}" y="{{ $f($y) + 4 }}" text-anchor="end" class="fill-muted" font-size="11">{{ $i * $step }}</text>
    @endfor
    @foreach ($pts as $i => [$x, $y])
        <text x="{{ $f($x) }}" y="{{ $H - 8 }}" text-anchor="middle" class="fill-muted" font-size="11">{{ $labels[$i] ?? '' }}</text>
    @endforeach
    @if ($area)<path d="{{ $area }}" fill="url(#{{ $gid }})"/>@endif
    @if ($line)<path d="{{ $line }}" fill="none" stroke="#3B7BF6" stroke-width="2.2" stroke-linecap="round"/>@endif
    @foreach ($pts as $i => [$x, $y])
        <circle cx="{{ $f($x) }}" cy="{{ $f($y) }}" r="3.6" fill="#3B7BF6" class="stroke-surface" stroke-width="1.5"/>
    @endforeach
    @if (count($pts))
        <g>
            <rect x="{{ $f($bx) }}" y="{{ $f($last[1] - 34) }}" width="68" height="22" rx="6" fill="#2563EB"/>
            <text x="{{ $f($bx + 34) }}" y="{{ $f($last[1] - 19) }}" text-anchor="middle" font-size="11" font-weight="600" fill="#fff">{{ end($values) }} {{ $unit }}</text>
        </g>
    @endif
</svg>
