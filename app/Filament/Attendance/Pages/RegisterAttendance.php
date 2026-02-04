<?php

namespace App\Filament\Attendance\Pages;

use App\Models\Personal;
use App\Models\Asistencias;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Actions\Action;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Pages\Page;
use Filament\Notifications\Notification;
use Filament\Support\Enums\ActionSize;
use Illuminate\Support\Facades\DB;

class RegisterAttendance extends Page implements HasForms
{
    use InteractsWithForms;

    protected static ?string $navigationIcon = 'heroicon-o-clock';
    protected static string $view = 'filament.attendance.pages.register-attendance';
    protected static ?string $navigationLabel = 'Registro de Asistencia';
    protected static ?string $title = 'Registro de Asistencia';

    public ?array $data = [];
    public $lastAttendance = null;
    public $todayStats = null;
    public $recentAttendances = [];
    public $showManualForm = false;
    public $selectedEmployee = null;

    public function mount(): void
    {
        $this->loadLastAttendance();
        $this->loadTodayStats();
        $this->loadRecentAttendances();
        
        // Inicializar el formulario con datos vacíos
        $this->form->fill([
            'document' => '',
            'employee_id' => null,
            'note' => ''
        ]);
    }

    public function loadLastAttendance(): void
    {
        $this->lastAttendance = Asistencias::with('personal')
            ->orderBy('day', 'desc')
            ->orderBy('hour', 'desc')
            ->first();
    }

    public function loadTodayStats(): void
    {
        $today = now()->toDateString();
        $this->todayStats = [
            'total' => Asistencias::where('day', $today)->count(),
            'manual' => Asistencias::where('day', $today)->where('record_type', 'MANUAL')->count(),
            'fingerprint' => Asistencias::where('day', $today)->where('record_type', 'HUELLA')->count(),
            'total_employees' => Personal::active()->count(),
        ];
    }

    public function loadRecentAttendances(): void
    {
        $this->recentAttendances = Asistencias::with('personal')
            ->orderBy('day', 'desc')
            ->orderBy('hour', 'desc')
            ->limit(5)
            ->get();
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                // Sección de estadísticas del día
                Section::make('Estadísticas del Día')
                    ->description(now()->translatedFormat('l, d \\d\\e F \\d\\e Y'))
                    ->schema([
                        Placeholder::make('total_today')
                            ->label('Registros Hoy')
                            ->content(fn() => $this->todayStats['total'] ?? 0)
                            ->extraAttributes(['class' => 'text-center text-2xl font-bold text-primary-600']),
                            
                        Placeholder::make('manual_today')
                            ->label('Manuales')
                            ->content(fn() => $this->todayStats['manual'] ?? 0)
                            ->extraAttributes(['class' => 'text-center']),
                            
                        Placeholder::make('fingerprint_today')
                            ->label('Huella')
                            ->content(fn() => $this->todayStats['fingerprint'] ?? 0)
                            ->extraAttributes(['class' => 'text-center']),
                            
                        Placeholder::make('pending_today')
                            ->label('Pendientes')
                            ->content(fn() => max(0, ($this->todayStats['total_employees'] ?? 0) - ($this->todayStats['total'] ?? 0)))
                            ->extraAttributes(['class' => 'text-center']),
                    ])
                    ->columns(4)
                    ->collapsible(),
                    
                // Sección de registro principal (cédula)
                Section::make('Registro Rápido por Cédula')
                    ->description('Ingrese el número de cédula para registro automático')
                    ->schema([
                        TextInput::make('document')
                            ->label('Número de Cédula')
                            ->required()
                            ->numeric()
                            ->placeholder('Ej: 123456789')
                            ->autofocus()
                            ->prefixIcon('heroicon-o-identification')
                            ->extraInputAttributes([
                                'style' => 'font-size: 1.5rem; text-align: center;',
                                'class' => 'text-center'
                            ])
                            ->live(debounce: 500)
                            ->afterStateUpdated(function ($state) {
                                if ($state && strlen($state) >= 8) {
                                    $this->registerAttendance($state, 'HUELLA');
                                }
                            }),
                    ])
                    ->columns(1),
                    
                // Sección de registro manual (SIEMPRE visible pero con contenido condicional)
                // Section::make('Registro Manual')
                //     ->description('Registro manual de asistencia')
                //     ->schema([
                //         Select::make('employee_id')
                //             ->label('Seleccionar Empleado')
                //             ->searchable()
                //             ->preload()
                //             ->options(function () {
                //                 return Personal::active()
                //                     ->get()
                //                     ->mapWithKeys(fn($employee) => [
                //                         $employee->id => "{$employee->document} - {$employee->name} {$employee->last_name}"
                //                     ])
                //                     ->toArray();
                //             })
                //             ->required()
                //             ->live()
                //             ->afterStateUpdated(function ($state) {
                //                 $this->selectedEmployee = Personal::find($state);
                //             }),
                            
                //         // Mostrar información del empleado solo si hay uno seleccionado
                //         Placeholder::make('employee_info')
                //             ->label('Información del Empleado')
                //             ->hidden(fn() => !$this->selectedEmployee)
                //             ->content(function () {
                //                 if (!$this->selectedEmployee) return '';
                                
                //                 $hasRegistered = Asistencias::hasRegisteredToday($this->selectedEmployee->id);
                //                 $statusColor = $hasRegistered ? 'text-red-600' : 'text-green-600';
                //                 $statusText = $hasRegistered ? 'Ya registró hoy' : 'Pendiente de registro';
                                
                //                 return "
                //                     <div class='space-y-1 p-3 bg-gray-50 rounded'>
                //                         <p><strong>Nombre:</strong> {$this->selectedEmployee->name} {$this->selectedEmployee->last_name}</p>
                //                         <p><strong>Cédula:</strong> {$this->selectedEmployee->document}</p>
                //                         <p><strong>Cargo:</strong> {$this->selectedEmployee->position}</p>
                //                         <p><strong>Estado:</strong> <span class='{$statusColor}'>{$statusText}</span></p>
                //                     </div>
                //                 ";
                //             }),
                            
                //         Textarea::make('note')
                //             ->label('Nota (Opcional)')
                //             ->placeholder('Ej: Llegó tarde, Salida temprana, Permiso, etc.')
                //             ->rows(2),
                            
                //         \Filament\Forms\Components\Actions::make([
                //             Action::make('register_manual')
                //                 ->label('Registrar Asistencia Manual')
                //                 ->icon('heroicon-o-check-circle')
                //                 ->color('success')
                //                 ->action(function ($state) {
                //                     $this->registerManualAttendance(
                //                         $state['employee_id'] ?? null, 
                //                         $state['note'] ?? null
                //                     );
                //                 }),
                //         ])->fullWidth(),
                //     ])
                //     ->columns(1),
                    
                // Últimos registros
                Section::make('Últimos Registros')
                    ->schema([
                        Placeholder::make('recent_list')
                            ->label('')
                            ->content(function () {
                                if ($this->recentAttendances->isEmpty()) {
                                    return '<p class="text-gray-500 text-center">No hay registros recientes</p>';
                                }
                                
                                $html = '<div class="space-y-2">';
                                foreach ($this->recentAttendances as $attendance) {
                                    $time = \Carbon\Carbon::parse($attendance->hour)->format('H:i');
                                    $typeColor = $attendance->record_type === 'MANUAL' ? 'text-orange-600' : 'text-blue-600';
                                    $typeText = $attendance->record_type === 'MANUAL' ? 'Manual' : 'Huella';
                                    
                                    $html .= "
                                        <div class='flex items-center justify-between p-2 bg-gray-50 rounded hover:bg-gray-100 transition'>
                                            <div class='flex items-center space-x-3'>
                                                <div class='{$typeColor} font-medium text-sm'>
                                                    {$typeText}
                                                </div>
                                                <div>
                                                    <p class='font-medium'>{$attendance->personal->name} {$attendance->personal->last_name}</p>
                                                    <p class='text-sm text-gray-500'>{$attendance->personal->document}</p>
                                                </div>
                                            </div>
                                            <div class='text-right'>
                                                <p class='font-medium'>{$time}</p>
                                                <p class='text-sm text-gray-500'>{$attendance->day}</p>
                                            </div>
                                        </div>
                                    ";
                                }
                                $html .= '</div>';
                                
                                return $html;
                            })
                            ->extraAttributes(['class' => 'mt-4']),
                    ])
                    ->collapsible(),
                    
                // Información del último registro
                Section::make('Último Registro Realizado')
                    ->schema([
                        Placeholder::make('last_employee_full')
                            ->label('Empleado')
                            ->content(fn() => $this->lastAttendance 
                                ? "{$this->lastAttendance->personal->name} {$this->lastAttendance->personal->last_name} ({$this->lastAttendance->personal->document})"
                                : 'No hay registros')
                            ->extraAttributes(['class' => 'font-medium']),
                            
                        Placeholder::make('last_datetime')
                            ->label('Fecha y Hora')
                            ->content(function () {
                                if (!$this->lastAttendance) return '--/--/---- --:--';
                                
                                $date = \Carbon\Carbon::parse($this->lastAttendance->day)->format('d/m/Y');
                                $time = \Carbon\Carbon::parse($this->lastAttendance->hour)->format('H:i:s');
                                return "{$date} a las {$time}";
                            }),
                            
                        Placeholder::make('last_type_badge')
                            ->label('Tipo')
                            ->content(function () {
                                if (!$this->lastAttendance) return '---';
                                
                                $type = $this->lastAttendance->record_type;
                                $color = $type === 'MANUAL' ? 'bg-orange-100 text-orange-800' : 'bg-blue-100 text-blue-800';
                                
                                return "
                                    <span class='inline-flex items-center px-2 py-1 rounded-full text-xs font-medium {$color}'>
                                        {$type}
                                    </span>
                                ";
                            }),
                    ])
                    ->columns(3)
                    ->collapsible(),
            ])
            ->statePath('data');
    }

    public function registerAttendance($document, $recordType = 'MANUAL'): void
    {
        DB::beginTransaction();
        
        try {
            // Buscar al personal por documento
            $personal = Personal::where('document', $document)->first();
            
            if (!$personal) {
                Notification::make()
                    ->title('Empleado no encontrado')
                    ->body("No se encontró un empleado con cédula: {$document}")
                    ->danger()
                    ->send();
                    
                $this->form->fill(['document' => '']);
                return;
            }
            
            // Verificar estado del empleado
            if (!$personal->canRegisterAttendance()) {
                Notification::make()
                    ->title('Empleado no autorizado')
                    ->body("El empleado {$personal->name} {$personal->last_name} no puede registrar asistencia. Estado: " . strtoupper($personal->status))
                    ->warning()
                    ->send();
                    
                $this->form->fill(['document' => '']);
                return;
            }
            
            // Verificar si ya registró hoy
            if (Asistencias::hasRegisteredToday($personal->id)) {
                Notification::make()
                    ->title('Atención: Ya registró hoy')
                    ->body("{$personal->name} {$personal->last_name} ya tiene un registro de asistencia hoy")
                    ->warning()
                    ->send();
            }
            
            // Registrar la asistencia
            $attendance = Asistencias::create([
                'id_personal' => $personal->id,
                'day' => now()->toDateString(),
                'hour' => now()->toTimeString(),
                'record_type' => $recordType,
            ]);
            
            DB::commit();
            
            // Mostrar notificación de éxito
            Notification::make()
                ->title('¡Asistencia registrada!')
                ->body("{$personal->name} {$personal->last_name} - " . now()->format('H:i:s') . " ({$recordType})")
                ->success()
                ->send();
            
            // Actualizar datos
            $this->loadLastAttendance();
            $this->loadTodayStats();
            $this->loadRecentAttendances();
            
            // Limpiar el campo de cédula
            $this->form->fill(['document' => '']);
            
        } catch (\Exception $e) {
            DB::rollBack();
            
            Notification::make()
                ->title('Error al registrar')
                ->body('Ocurrió un error: ' . $e->getMessage())
                ->danger()
                ->send();
        }
    }
    
    public function registerManualAttendance($employeeId, $note = null): void
    {
        DB::beginTransaction();
        
        try {
            $personal = Personal::find($employeeId);
            
            if (!$personal) {
                Notification::make()
                    ->title('Error')
                    ->body('Empleado no encontrado')
                    ->danger()
                    ->send();
                return;
            }
            
            // Verificar estado del empleado
            if (!$personal->canRegisterAttendance()) {
                Notification::make()
                    ->title('Empleado no autorizado')
                    ->body("El empleado no puede registrar asistencia. Estado: " . strtoupper($personal->status))
                    ->warning()
                    ->send();
                return;
            }
            
            // Registrar la asistencia manual
            $attendance = Asistencias::create([
                'id_personal' => $personal->id,
                'day' => now()->toDateString(),
                'hour' => now()->toTimeString(),
                'record_type' => 'MANUAL',
                'note' => $note,
            ]);
            
            DB::commit();
            
            // Mostrar notificación de éxito
            Notification::make()
                ->title('¡Asistencia manual registrada!')
                ->body("{$personal->name} {$personal->last_name} - " . now()->format('H:i:s'))
                ->success()
                ->send();
            
            // Actualizar datos
            $this->loadLastAttendance();
            $this->loadTodayStats();
            $this->loadRecentAttendances();
            
            // Resetear formulario manual
            $this->selectedEmployee = null;
            $this->form->fill(['employee_id' => null, 'note' => '']);
            
        } catch (\Exception $e) {
            DB::rollBack();
            
            Notification::make()
                ->title('Error al registrar manualmente')
                ->body('Ocurrió un error: ' . $e->getMessage())
                ->danger()
                ->send();
        }
    }

    protected function getFormActions(): array
    {
        return [];
    }

    public static function shouldRegisterNavigation(): bool
    {
        return true;
    }

    public static function getNavigationSort(): ?int
    {
        return 1;
    }
}