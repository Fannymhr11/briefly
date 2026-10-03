@props(['status', 'type' => 'brief'])
@php
$map = $type === 'task'
    ? ['pending' => ['Belum Dikerjakan', 'badge-amber'], 'in_progress' => ['Sedang Diproses', 'badge-blue'], 'completed' => ['Sudah Selesai', 'badge-green']]
    : ['pending' => ['Menunggu Review', 'badge-amber'], 'approved' => ['Disetujui', 'badge-green'], 'rejected' => ['Ditolak', 'badge-red']];
[$label, $cls] = $map[$status] ?? [$status, 'badge-gray'];
@endphp
<span {{ $attributes->class(['badge badge-dot', $cls]) }}>{{ $label }}</span>
