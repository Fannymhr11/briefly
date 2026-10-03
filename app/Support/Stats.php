<?php

namespace App\Support;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Carbon;

class Stats
{
    /** Jumlah record per hari untuk N hari terakhir. */
    public static function trend(Builder $query, int $days = 7): array
    {
        $start = now()->subDays($days - 1)->startOfDay();
        $counts = (clone $query)->where('created_at', '>=', $start)->pluck('created_at')
            ->countBy(fn ($d) => Carbon::parse($d)->format('Y-m-d'));

        $labels = [];
        $values = [];
        for ($i = 0; $i < $days; $i++) {
            $day = $start->copy()->addDays($i);
            $labels[] = $day->translatedFormat('j M');
            $values[] = (int) ($counts[$day->format('Y-m-d')] ?? 0);
        }

        return ['labels' => $labels, 'values' => $values];
    }

    /** Selisih jumlah record bulan ini vs bulan lalu (untuk label "dari bulan lalu"). */
    public static function delta(Builder $query): int
    {
        $thisMonth = (clone $query)->where('created_at', '>=', now()->startOfMonth())->count();
        $lastMonth = (clone $query)
            ->whereBetween('created_at', [now()->subMonthNoOverflow()->startOfMonth(), now()->subMonthNoOverflow()->endOfMonth()])
            ->count();

        return $thisMonth - $lastMonth;
    }

    /** Ambil filter GET yang valid dari request. */
    public static function filters(\Illuminate\Http\Request $request): array
    {
        return $request->validate([
            'q' => ['nullable', 'string', 'max:100'],
            'status' => ['nullable', 'string', 'max:20'],
            'brand' => ['nullable', 'string', 'max:20'],
            'platform' => ['nullable', 'string', 'max:30'],
            'action' => ['nullable', 'string', 'max:40'],
            'user' => ['nullable', 'integer'],
            'from' => ['nullable', 'date'],
            'to' => ['nullable', 'date'],
            'role' => ['nullable', 'string', 'max:20'],
        ]);
    }
}
