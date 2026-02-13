<?php

namespace App\Filament\Widgets;

use App\Models\Attendance;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\DB;

class AttendanceByTimeChart extends ChartWidget
{
    protected static ?string $heading = 'Asistencias en los últimos 5 días';

    protected static ?int $sort = 3;

    protected function getData(): array
    {
        // ✅ Obtener los últimos 5 días con registros
        $records = Attendance::query()
            ->selectRaw('DATE(day) as date, COUNT(*) as total')
            ->groupBy(DB::raw('DATE(day)'))
            ->orderByDesc('date')
            ->limit(5)
            ->get()
            ->reverse(); // Para mostrar en orden cronológico

        $labels = $records->pluck('date')
            ->map(fn ($date) => \Carbon\Carbon::parse($date)->format('d M'))
            ->toArray();

        $data = $records->pluck('total')->toArray();

        if (empty($labels)) {
            $labels = ['Sin registros'];
            $data = [0];
        }

        return [
            'datasets' => [
                [
                    'label' => 'Personas que marcaron asistencia',
                    'data' => $data,
                    'backgroundColor' => '#6366F1',
                    'borderRadius' => 6,
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
            'plugins' => [
                'legend' => [
                    'display' => false,
                ],
                'tooltip' => [
                    'callbacks' => [
                        'label' => "function(context) {
                            var value = context.raw || 0;
                            return value + ' asistencias';
                        }",
                    ],
                ],
            ],

            // ✅ Solo números enteros en el eje Y
            'scales' => [
                'y' => [
                    'beginAtZero' => true,
                    'ticks' => [
                        'precision' => 0,
                        'stepSize' => 1,
                    ],
                ],
            ],

            'animation' => [
                'duration' => 800,
            ],
        ];
    }
}
