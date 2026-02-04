<x-filament-panels::page>
    <x-filament::section>
        <div class="space-y-8">
            <!-- Encabezado -->
            <div class="text-center">
                <div class="flex items-center justify-center mb-3">
                    <x-filament::icon 
                        icon="heroicon-o-clock" 
                        class="h-10 w-10 text-primary-600 mr-3" 
                    />
                    <h1 class="text-3xl font-bold text-gray-900 dark:text-white">
                        Sistema de Registro de Asistencia
                    </h1>
                </div>
                <p class="text-lg text-gray-600 dark:text-gray-400">
                    Registre su asistencia de forma rápida y segura
                </p>
            </div>
            
            <!-- Contenido principal -->
            {{ $this->form }}
            
            <!-- Información adicional -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mt-8">
                <div class="bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-lg p-4">
                    <div class="flex items-center">
                        <x-filament::icon 
                            icon="heroicon-o-user-group" 
                            class="h-8 w-8 text-blue-600 mr-3" 
                        />
                        <div>
                            <h3 class="font-semibold text-blue-900 dark:text-blue-100">Total Empleados</h3>
                            <p class="text-2xl font-bold text-blue-700 dark:text-blue-300">
                                {{ $this->todayStats['total_employees'] ?? 0 }}
                            </p>
                        </div>
                    </div>
                </div>
                
                <div class="bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 rounded-lg p-4">
                    <div class="flex items-center">
                        <x-filament::icon 
                            icon="heroicon-o-check-circle" 
                            class="h-8 w-8 text-green-600 mr-3" 
                        />
                        <div>
                            <h3 class="font-semibold text-green-900 dark:text-green-100">Registrados Hoy</h3>
                            <p class="text-2xl font-bold text-green-700 dark:text-green-300">
                                {{ $this->todayStats['total'] ?? 0 }}
                            </p>
                        </div>
                    </div>
                </div>
                
                <div class="bg-amber-50 dark:bg-amber-900/20 border border-amber-200 dark:border-amber-800 rounded-lg p-4">
                    <div class="flex items-center">
                        <x-filament::icon 
                            icon="heroicon-o-clock" 
                            class="h-8 w-8 text-amber-600 mr-3" 
                        />
                        <div>
                            <h3 class="font-semibold text-amber-900 dark:text-amber-100">Hora Actual</h3>
                            <p class="text-2xl font-bold text-amber-700 dark:text-amber-300" id="currentTime">
                                {{ now()->format('H:i:s') }}
                            </p>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Instrucciones -->
            <div class="mt-8 p-4 bg-gray-50 dark:bg-gray-800 rounded-lg">
                <h3 class="font-semibold text-gray-900 dark:text-white mb-2">
                    <x-filament::icon icon="heroicon-o-information-circle" class="h-5 w-5 inline mr-2" />
                    Instrucciones:
                </h3>
                <ul class="list-disc list-inside text-gray-600 dark:text-gray-400 space-y-1">
                    <li>Para registro rápido: Ingrese su cédula y presione Enter</li>
                    <li>Para registro manual: Use el botón "Registro Manual"</li>
                    <li>Verifique que su estado sea "Activo" o "Autorizado"</li>
                    <li>Contacte a recursos humanos si tiene problemas</li>
                </ul>
            </div>
        </div>
    </x-filament::section>
    
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Actualizar hora en tiempo real
            function updateTime() {
                const now = new Date();
                const timeString = now.toLocaleTimeString('es-ES', { 
                    hour: '2-digit', 
                    minute: '2-digit',
                    second: '2-digit' 
                });
                const timeElement = document.getElementById('currentTime');
                if (timeElement) {
                    timeElement.textContent = timeString;
                }
            }
            
            setInterval(updateTime, 1000);
            
            // Auto-focus en el campo de cédula
            const documentInput = document.querySelector('input[name="data.document"]');
            if (documentInput) {
                documentInput.focus();
                
                // Auto-limpiar después de 3 segundos si hay éxito
                setTimeout(() => {
                    if (documentInput.value && !documentInput.hasAttribute('disabled')) {
                        documentInput.value = '';
                        documentInput.focus();
                    }
                }, 3000);
            }
            
            // Notificación de audio (opcional)
            function playSuccessSound() {
                try {
                    const audio = new Audio('https://assets.mixkit.co/sfx/preview/mixkit-correct-answer-tone-2870.mp3');
                    audio.volume = 0.3;
                    audio.play();
                } catch (e) {
                    // Silenciar error si no se puede reproducir audio
                }
            }
            
            // Escuchar eventos Livewire para reproducir sonido
            window.addEventListener('attendance-registered', function() {
                playSuccessSound();
            });
        });
    </script>
    
    <style>
        /* Estilos personalizados */
        .fi-section-header-heading {
            font-size: 1.25rem !important;
            font-weight: 600 !important;
        }
        
        input[name="data.document"] {
            height: 3.5rem !important;
            font-size: 1.5rem !important;
            letter-spacing: 0.05em;
            border-radius: 0.75rem !important;
            border-width: 2px !important;
            transition: all 0.3s ease;
        }
        
        input[name="data.document"]:focus {
            border-color: var(--primary-500) !important;
            box-shadow: 0 0 0 3px rgba(var(--primary-500-rgb), 0.1) !important;
            transform: translateY(-2px);
        }
        
        .badge {
            display: inline-flex;
            align-items: center;
            padding: 0.25rem 0.75rem;
            border-radius: 9999px;
            font-size: 0.75rem;
            font-weight: 600;
            text-transform: uppercase;
        }
        
        .badge-manual {
            background-color: rgba(249, 115, 22, 0.1);
            color: rgb(249, 115, 22);
        }
        
        .badge-fingerprint {
            background-color: rgba(59, 130, 246, 0.1);
            color: rgb(59, 130, 246);
        }
        
        .stat-card {
            transition: all 0.3s ease;
        }
        
        .stat-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1);
        }
        
        .fade-in {
            animation: fadeIn 0.5s ease-in;
        }
        
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }
        
        .pulse-animation {
            animation: pulse 2s infinite;
        }
        
        @keyframes pulse {
            0% { box-shadow: 0 0 0 0 rgba(var(--primary-500-rgb), 0.4); }
            70% { box-shadow: 0 0 0 10px rgba(var(--primary-500-rgb), 0); }
            100% { box-shadow: 0 0 0 0 rgba(var(--primary-500-rgb), 0); }
        }
    </style>
</x-filament-panels::page>