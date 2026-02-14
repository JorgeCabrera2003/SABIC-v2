<x-filament-widgets::widget>
    <x-filament::section>
        <div x-data="{ 
                time: new Date().toLocaleTimeString('es-ES', { hour: '2-digit', minute: '2-digit', second: '2-digit' }),
                date: new Date().toLocaleDateString('es-ES', { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' })
            }" x-init="setInterval(() => {
                time = new Date().toLocaleTimeString('es-ES', { hour: '2-digit', minute: '2-digit', second: '2-digit' });
                date = new Date().toLocaleDateString('es-ES', { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' });
            }, 1000)" class="flex flex-col items-center justify-center space-y-2 text-center">
            <div class="text-4xl font-bold tracking-tight text-primary-600 dark:text-primary-500" x-text="time"></div>
            <div class="text-lg font-medium text-gray-500 dark:text-gray-400 capitalize" x-text="date"></div>
        </div>
    </x-filament::section>
</x-filament-widgets::widget>