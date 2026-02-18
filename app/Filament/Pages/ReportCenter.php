<?php

namespace App\Filament\Pages;

use App\Models\Attendance;
use Filament\Pages\Page;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Illuminate\Support\Carbon;
use Filament\Actions\Action;
use Filament\Forms\Contracts\HasForms;
use Filament\Tables\Contracts\HasTable;
use Filament\Forms\Components\Section;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Tables\Concerns\InteractsWithTable;
use Torgodly\Html2Media\Actions\Html2MediaAction;

class ReportCenter extends Page implements HasForms, HasTable
{
    use InteractsWithForms;
    use InteractsWithTable;

    protected static ?string $navigationIcon = 'heroicon-o-document-chart-bar';
    protected static string $view = 'filament.pages.report-center';
    protected static ?string $title = 'Centro de Reportes';

    public ?array $data = [];

    public function mount(): void
    {
        $this->form->fill([
            'fecha_inicio' => now()->startOfMonth()->toDateString(),
            'fecha_fin' => now()->endOfMonth()->toDateString(),
        ]);
    }

    public function form(Form $form): Form
    {
        return $form->schema([
            Section::make('Filtros de Búsqueda')
                ->schema([
                    DatePicker::make('fecha_inicio')
                        ->label('Fecha Inicio')
                        ->native(false)
                        ->live() 
                        ->required(),
                    DatePicker::make('fecha_fin')
                        ->label('Fecha Fin')
                        ->native(false)
                        ->live() 
                        ->required(),
                ])->columns(2),
        ])->statePath('data');
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(
                Attendance::query()
                    ->with(['personal.nominalLocation'])
                    ->whereBetween('day', [
                        $this->data['fecha_inicio'] ?? now()->startOfMonth(),
                        Carbon::parse($this->data['fecha_fin'] ?? now())->endOfDay()
                    ])
                    ->orderBy('day', 'desc')
            )
            ->columns([
                TextColumn::make('personal.document')->label('Cédula')->searchable(),
                TextColumn::make('personal.name')
                    ->label('Empleado')
                    ->formatStateUsing(fn ($record) => "{$record->personal?->name} {$record->personal?->last_name}"),
                TextColumn::make('personal.nominalLocation.name')->label('Ubicación'),
                TextColumn::make('day')->label('Fecha')->date('d/m/Y'),
                TextColumn::make('hour')->label('Hora')->time('g:i A'),
                TextColumn::make('record_type')
                    ->label('Tipo')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'Entrada' => 'success',
                        'Salida' => 'danger',
                        default => 'gray',
                    }),
            ])
            ->paginated([10, 25, 50]);
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('exportExcel')
                ->label('Exportar Excel')
                ->color('success')
                ->icon('heroicon-o-table-cells')
                ->action(function () {
                    // Sincroniza datos del formulario
                    $formData = $this->form->getState();
                    
                    $start = Carbon::parse($formData['fecha_inicio']);
                    $end = Carbon::parse($formData['fecha_fin'])->endOfDay();

                    $registros = Attendance::with(['personal.nominalLocation'])
                        ->whereBetween('day', [$start, $end])
                        ->orderBy('day', 'desc')
                        ->get();

                    $nombreArchivo = 'reporte-' . now()->format('dmY-His') . '.csv';

                    return response()->streamDownload(function () use ($registros) {
                        $file = fopen('php://output', 'w');
                        fputs($file, chr(0xEF) . chr(0xBB) . chr(0xBF));
                        fputcsv($file, ['Cédula', 'Nombre', 'Apellido', 'Ubicación', 'Fecha', 'Hora', 'Tipo', 'Observación'], ';');

                        foreach ($registros as $row) {
                            fputcsv($file, [
                                $row->personal?->document ?? 'N/A',
                                $row->personal?->name ?? '',
                                $row->personal?->last_name ?? '',
                                $row->personal?->nominalLocation?->name ?? 'N/A',
                                Carbon::parse($row->day)->format('d/m/Y'),
                                Carbon::parse($row->hour)->format('g:i A'),
                                $row->record_type,
                                $row->observation ?: '-'
                            ], ';');
                        }
                        fclose($file);
                    }, $nombreArchivo);
                }),

            Html2MediaAction::make('generateReport')
                ->label('Generar PDF')
                ->color('info')
                ->preview()
                ->savePdf()
                ->content(fn () => view('reports.general-stats', [
                    'data' => $this->getReportData(),
                ])),
        ];
    }

    protected function getReportData(): array
    {
        // Importante: usamos getState() para asegurar que el PDF use las fechas actuales
        $formData = $this->form->getState();
        $start = Carbon::parse($formData['fecha_inicio']);
        $end = Carbon::parse($formData['fecha_fin'])->endOfDay();

        $registros = Attendance::with(['personal.nominalLocation'])
            ->whereBetween('day', [$start, $end])
            ->orderBy('day', 'desc')
            ->get();

        return [
            'titulo' => 'Reporte de Asistencia',
            'fecha_generacion' => now(),
            'periodo' => "{$start->format('d/m/Y')} - {$end->format('d/m/Y')}",
            'registros' => $registros,
            'total_registros' => $registros->count(), // Volvemos a agregar la clave por si acaso
        ];
    }
}