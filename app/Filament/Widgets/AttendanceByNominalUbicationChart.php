<?php

namespace App\Filament\Widgets;

use App\Models\Attendance;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\DB;

class AttendanceByNominalUbicationChart extends ChartWidget
{
    protected int|string|array $columnSpan = 'full';

    protected static ?string $heading = 'Asistencias por Ubicación Nominal';

    protected static ?int $sort = 2;

    protected function getData(): array
    {
        $attendancesByLocation = Attendance::query()
            ->whereDate('day', today())
            ->join('personals', 'attendance.id_personal', '=', 'personals.id')
            ->join('nominal_location', 'personals.id_nominal_location', '=', 'nominal_location.id')
            ->select('nominal_location.name', DB::raw('count(*) as total'))
            ->groupBy('nominal_location.id', 'nominal_location.name')
            ->orderBy('total', 'desc')
            ->get();

        $labels = $attendancesByLocation->pluck('name')->toArray();
        $data = $attendancesByLocation->pluck('total')->toArray();

        if (empty($labels)) {
            $labels = ['Sin asistencias hoy'];
            $data = [1];
        }

        // 🎨 Paleta moderna (gradiente tipo dashboard profesional)
        $colors = [
            '#6366F1', // Indigo
            '#22C55E', // Green
            '#F59E0B', // Amber
            '#EF4444', // Red
            '#06B6D4', // Cyan
            '#A855F7', // Purple
            '#F97316', // Orange
            '#14B8A6', // Teal
        ];

        return [
            'datasets' => [
                [
                    'label' => 'Asistencias hoy',
                    'data' => $data,
                    'backgroundColor' => $colors,
                    'borderWidth' => 0,
                    'hoverOffset' => 12,
                ],
            ],
            'labels' => $labels,
        ];
    }

    protected function getType(): string
    {
        return 'bar';
    }

    protected function getOptions(): array
    {
        return [
            'cutout' => '65%',
            'plugins' => [
                'legend' => [
                    'position' => 'bottom',
                    'labels' => [
                        'padding' => 16,
                        'boxWidth' => 14,
                        'font' => [
                            'size' => 12,
                        ],
                    ],
                ],
                'tooltip' => [
                    'backgroundColor' => '#111827',
                    'padding' => 12,
                    'callbacks' => [
                        'label' => "function(context) {
                        var label = context.label || '';
                        var value = context.raw || 0;
                        var total = context.dataset.data.reduce((a, b) => a + b, 0);
                        var percentage = Math.round((value / total) * 100);
                        return label + ': ' + value + ' (' + percentage + '%)';
                    }",
                    ],
                ],
            ],

            // ✅ FORZAR ENTEROS
            'scales' => [
                'y' => [
                    'ticks' => [
                        'precision' => 0,
                        'stepSize' => 1,
                    ],
                ],
            ],

            'animation' => [
                'animateRotate' => true,
                'duration' => 1000,
            ],
            'layout' => [
                'padding' => 10,
            ],
        ];
    }

    public function getHeading(): string
    {
        $total = Attendance::whereDate('day', today())->count();

        return static::$heading." • Total: $total";
    }
}
