<?php

namespace App\Filament\Attendance\Pages;

use App\Models\Personal;
use App\Models\Attendance;
use Filament\Pages\Page;
use Filament\Forms\Form;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Actions\Action;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Notifications\Notification;
use Illuminate\Support\HtmlString;
use Illuminate\Support\Facades\DB;

use BezhanSalleh\FilamentShield\Traits\HasPageShield;

class RegisterAttendance extends Page implements HasForms
{
    use InteractsWithForms, HasPageShield;

    protected static ?string $navigationIcon = 'heroicon-o-clock';
    protected static ?string $navigationLabel = 'Marcar Asistencia';
    protected static ?string $title = 'Control de Acceso';

    protected static string $view = 'filament.attendance.pages.register-attendance';

    public ?array $data = [];

    public ?Attendance $lastRecord = null;

    public function mount(): void
    {
        $this->form->fill();
        // Cargar el último registro del día al entrar a la página
        $this->refreshLastRecord();
    }

    public function refreshLastRecord()
    {
        // Cargamos la asistencia con el personal y sus detalles
        $this->lastRecord = Attendance::with(['personal.position', 'personal.nominalLocation'])
            ->where('day', now()->toDateString())
            ->latest('hour')
            ->first();
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Grid::make(3)
                    ->schema([
                        // === COLUMNA IZQUIERDA: FORMULARIO DE ENTRADA ===
                        Section::make('Nuevo Registro')
                            ->columnSpan(1)
                            ->schema([
                                TextInput::make('document')
                                    ->label('Cédula de Identidad')
                                    ->required()
                                    ->mask('999999999')
                                    ->placeholder('Ej: 123456789')
                                    ->length(8)
                                    ->regex('/^[0-9]+$/')
                                    ->unique(ignoreRecord: true)
                                    ->validationMessages([
                                        'length' => 'La cédula debe tener exactamente 9 dígitos.',
                                        'regex' => 'La cédula solo puede contener números.',
                                        'unique' => 'Esta cédula ya está registrada.',
                                    ])
                                    ->extraInputAttributes([
                                        'onkeydown' => 'if(event.key === "Enter") { $wire.registerAttendance(); event.preventDefault(); }'
                                    ]) // 3. Llama a la función correcta y evita el recargo de página
                                    ->extraInputAttributes(['style' => 'font-size: 1.2rem; font-weight: bold;']),

                                TextInput::make('observation')
                                    ->label('Observación')
                                    ->required()
                                    ->placeholder('Ej: Entrada regular')
                                    ->default('Entrada turno regular'), // Valor por defecto para agilizar

                                // Botón de acción manual
                                \Filament\Forms\Components\Actions::make([
                                    Action::make('save_attendance')
                                        ->label('MARCAR ENTRADA')
                                        ->icon('heroicon-o-arrow-right-circle')
                                        ->color('primary')
                                        ->size('xl')
                                        ->extraAttributes(['class' => 'w-full justify-center py-3']) // Botón grande
                                        ->action(fn() => $this->save()),
                                ]),
                            ]),

                        // === COLUMNA DERECHA: FICHA DEL ÚLTIMO REGISTRO ===
                        Section::make('Último Registro')
                            ->description('Información del trabajador procesado')
                            ->schema([
                                Placeholder::make('display_last_record')
                                    ->label('')
                                    ->content(function () {
                                        if (!$this->lastRecord) {
                                            return new HtmlString('<div class="text-center py-4 text-gray-400 italic">No hay registros hoy</div>');
                                        }

                                        $p = $this->lastRecord->personal;
                                        $photo = $p->photo_dir ? asset('storage/' . $p->photo_dir) : asset('img/default.png');

                                        // Formatear hora de entrada
                                        $hora = date('h:i A', strtotime($this->lastRecord->hour));

                                        return new HtmlString("
                                            <div class='flex items-center gap-5 p-4 bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm'>
                                                
                                                <div class='relative w-1/2 h-1/2 mr-4' style='margin-right: 1rem;'>
                                                    <img src='{$photo}' class='w-full h-full rounded-xl object-cover border-2 border-gray-100 dark:border-gray-600 shadow-sm' />
                                                </div>

                                                <div class='flex-1 min-w-0'> <div class='flex justify-between items-start ml-2'>
                                                        <div>
                                                            <h3 class='text-lg font-bold text-gray-900 dark:text-white truncate'>{$p->name} {$p->last_name}</h3>
                                                            <p class='text-lg font-mono text-gray-500 bg-gray-100 dark:bg-gray-700 px-2 py-0.5 rounded inline-block mb-2'>
                                                                CI: {$p->document}
                                                            </p>
                                                        </div>
                                                        
                                                        <div class='text-right'>
                                                            <p class='text-xl font-black text-green-600 leading-none'>{$hora}</p>
                                                            <p class='text-[10px] text-gray-400 uppercase font-bold tracking-wider'>Entrada</p>
                                                        </div>
                                                    </div>

                                                    <hr class='my-2 border-gray-100 dark:border-gray-700'>

                                                    <div class='grid grid-cols-2 gap-4 text-sm'>
                                                        <div>
                                                            <p class='text-[10px] text-gray-400 uppercase font-bold'>Cargo</p>
                                                            <p class='font-medium text-gray-700 dark:text-gray-300 truncate'>" . ($p->position->name ?? '---') . "</p>
                                                        </div>
                                                        <div>
                                                            <p class='text-[10px] text-gray-400 uppercase font-bold'>Ubicación</p>
                                                            <p class='font-medium text-gray-700 dark:text-gray-300 truncate'>" . ($p->nominalLocation->name ?? '---') . "</p>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        ");
                                    })
                            ])->columnSpan(2)
                    ]),
            ])
            ->statePath('data');
    }

    public function save()
    {
        // 1. Validar los datos del formulario
        $state = $this->form->getState();
        $cedula = $state['document'];
        $obs = $state['observation'];

        // 2. Buscar al empleado
        $empleado = Personal::where('document', $cedula)->first();

        if (!$empleado) {
            Notification::make()
                ->title('Personal no encontrado')
                ->body("No existe empleado con la cédula: $cedula")
                ->danger()
                ->persistent() // Se queda hasta que lo cierres
                ->send();

            // Enfocar de nuevo para corregir
            return;
        }

        if (!in_array($empleado->status, ['active', 'authorized'])) {
            $statusLabel = match ($empleado->status) {
                'inactive' => 'INACTIVO',
                'vacation' => 'EN VACACIONES',
                'unauthorized' => 'NO AUTORIZADO',
                default => strtoupper($empleado->status),
            };

            Notification::make()
                ->title('Acceso Denegado')
                ->body("El trabajador se encuentra en estatus: {$statusLabel}. No puede registrar asistencia.")
                ->warning() // Color naranja para advertencia
                ->persistent()
                ->send();

            $this->form->fill(['document' => '']); // Limpiar para el siguiente
            return;
        }

        // 2. Buscar al empleado
        $empleado = Attendance::where('id_personal', $empleado->id)->where('day', now()->toDateString())->first();

        if ($empleado) {
            Notification::make()
                ->title('Ya registrado')
                ->body("El trabajador ya registró su entrada hoy.")
                ->danger()
                ->persistent() // Se queda hasta que lo cierres
                ->send();

            // Enfocar de nuevo para corregir
            return;
        }

        // 3. Registrar Asistencia
        DB::beginTransaction();
        try {
            Attendance::create([
                'id_personal' => $empleado->id,
                'day' => now()->toDateString(),
                'hour' => now()->toTimeString(),
                'record_type' => 'MANUAL',
                'observation' => $obs,
            ]);

            DB::commit();

            // 4. Notificar éxito
            Notification::make()
                ->title('Entrada Exitosa')
                ->success()
                ->duration(2000) // Se quita rápido
                ->send();

            // 5. Actualizar la vista de la derecha y limpiar formulario
            $this->refreshLastRecord(); // Recarga la ficha derecha
            $this->form->fill([
                'document' => '',
                'observation' => 'Entrada turno regular' // Dejar la obs por defecto lista para el siguiente
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            Notification::make()->title('Error')->body($e->getMessage())->danger()->send();
        }
    }
}
