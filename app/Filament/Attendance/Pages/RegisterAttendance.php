<?php

namespace App\Filament\Attendance\Pages;

use App\Models\Personal;
use App\Models\Asistencias;
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

class RegisterAttendance extends Page implements HasForms
{
    use InteractsWithForms;

    protected static ?string $navigationIcon = 'heroicon-o-clock';
    protected static ?string $navigationLabel = 'Marcar Asistencia';
    protected static ?string $title = 'Control de Acceso';

    protected static string $view = 'filament.attendance.pages.register-attendance';

    public ?array $data = [];

    // Variable para almacenar el último registro procesado
    public ?Asistencias $lastRecord = null;

    public function mount(): void
    {
        $this->form->fill();
        // Cargar el último registro del día al entrar a la página
        $this->refreshLastRecord();
    }

    public function refreshLastRecord()
    {
        $this->lastRecord = Asistencias::with('personal')
            ->where('day', now()->toDateString())
            ->latest('id') // El último creado
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
                                    ->label('Cédula')
                                    ->required()
                                    ->numeric()
                                    ->autofocus() // El cursor inicia aquí siempre
                                    ->placeholder('Ingrese documento...')
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
                        Section::make('Último Ingreso Registrado')
                            ->columnSpan(2)
                            ->schema([
                                Placeholder::make('display_last')
                                    ->hiddenLabel()
                                    ->content(function () {
                                        if (!$this->lastRecord) {
                                            return new HtmlString('
                                                <div class="flex flex-col items-center justify-center p-10 text-gray-400 border-2 border-dashed border-gray-300 rounded-lg">
                                                    <span class="text-lg">Esperando registro...</span>
                                                </div>
                                            ');
                                        }

                                        $p = $this->lastRecord->personal;
                                        $fotoPath = $p->photo_dir ?: 'fotos-personal/default.png';
                                        $fotoUrl = asset('storage/' . $fotoPath);

                                        $hora = \Carbon\Carbon::parse($this->lastRecord->hour)->format('h:i A');

                                        return new HtmlString("
                                            <div class='flex flex-col md:flex-row gap-4 items-center bg-white dark:bg-gray-800 p-2 rounded-xl border border-gray-100 dark:border-gray-700'>
                                                
                                                <div class='w-32 h-32 flex-shrink-0 overflow-hidden rounded-lg shadow-sm border-2 border-primary-500'>
                                                    <img src='{$fotoUrl}' 
                                                        class='w-full h-full object-cover' 
                                                        style='aspect-ratio: 1/1;'
                                                        alt='Foto de {$p->name}'>
                                                </div>

                                                <div class='flex-1 min-w-0 px-2'>
                                                    <div class='mb-1'>
                                                        <span class='text-[10px] font-bold uppercase tracking-wider px-2 py-0.5 bg-success-500/10 text-success-600 rounded-full'>
                                                            Entrada Confirmada
                                                        </span>
                                                    </div>
                                                    <h2 class='text-xl font-bold text-gray-900 dark:text-white truncate'>
                                                        {$p->name} {$p->last_name}
                                                    </h2>
                                                    <div class='grid grid-cols-2 gap-x-4 gap-y-1 mt-2 text-sm'>
                                                        <div>
                                                            <p class='text-gray-400 text-[10px] uppercase'>Cédula</p>
                                                            <p class='font-medium'>{$p->document}</p>
                                                        </div>
                                                        <div>
                                                            <p class='text-gray-400 text-[10px] uppercase'>Hora</p>
                                                            <p class='font-bold text-primary-600'>{$hora}</p>
                                                        </div>
                                                        <div class='col-span-2'>
                                                            <p class='text-gray-400 text-[10px] uppercase'>Observación</p>
                                                            <p class='italic text-gray-600 truncate text-xs'>\"{$this->lastRecord->observation}\"</p>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        ");
                                    })
                            ])
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

        // 3. Registrar Asistencia
        DB::beginTransaction();
        try {
            Asistencias::create([
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
