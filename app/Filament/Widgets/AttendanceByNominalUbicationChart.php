<?php

namespace App\Filament\Widgets;

use App\Models\Attendance;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\DB;
use BezhanSalleh\FilamentShield\Traits\HasWidgetShield;

class AttendanceByNominalUbicationChart extends ChartWidget
{
    use HasWidgetShield;

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

        $dateLabel = ucfirst(now()->locale('es')->translatedFormat('d \d\e F \d\e Y'));
        $labels = $attendancesByLocation->map(fn() => $dateLabel)->toArray();
        $data = $attendancesByLocation->pluck('total')->toArray();

        if (empty($labels)) {
            $labels = ['Sin asistencias hoy'];
            $data = [0];
        }

        // 🎨 Paleta moderna (gradiente tipo dashboard profesional)
        $colors = [
            '#F59E0B', // Amber
            '#6366F1', // Indigo
            '#22C55E', // Green
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
            'cutout' => '65%',
            'plugins' => [
                'legend' => [
                    'display' => false,
                    // 'position' => 'bottom',
                    // 'labels' => [
                    //     'padding' => 16,
                    //     'boxWidth' => 14,
                    //     'font' => [
                    //         'size' => 12,
                    //     ],
                    // ],
                ],
                'tooltip' => [
                    'enabled' => true,
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

        return static::$heading . " • Total: $total";
    }
}
