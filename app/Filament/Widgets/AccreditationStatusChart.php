<?php

namespace App\Filament\Widgets;

use App\Models\Accreditation;
use Filament\Widgets\PieChartWidget;

class AccreditationStatusChart extends PieChartWidget
{
    protected ?string $heading = null;

    protected int | string | array $columnSpan = '1';

    public function getHeading(): ?string
    {
        return __('Accreditation Status Distribution');
    }

    protected function getData(): array
    {
        $statuses = Accreditation::query()->selectRaw('status, COUNT(*) as count')
            ->groupBy('status')
            ->pluck('count', 'status')
            ->toArray();

        // Define colors for common statuses
        $colors = [
            'published' => '#10b981', // green
            'draft' => '#f59e0b',     // yellow
            'pending' => '#3b82f6',    // blue
            'rejected' => '#ef4444',   // red
        ];

        return [
            'datasets' => [
                [
                    'label' => __('Accreditations by Status'),
                    'data' => array_values($statuses),
                    'backgroundColor' => array_map(fn ($status) => $colors[$status] ?? '#6b7280', array_keys($statuses)),
                ],
            ],
            'labels' => array_keys($statuses),
        ];
    }

    protected ?string $maxHeight = '300px';
}
