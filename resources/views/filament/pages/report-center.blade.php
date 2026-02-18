<x-filament-panels::page>
    <x-filament-panels::form wire:submit.prevent="submit">
        {{ $this->form }}
    </x-filament-panels::form>

    <div class="mt-6 border-t pt-6">
        <div class="flex items-center justify-between mb-4">
            <h2 class="text-xl font-bold tracking-tight">Vista Previa (Hojas de Cálculo)</h2>
            <p class="text-sm text-gray-500">Los datos se actualizan automáticamente al cambiar las fechas.</p>
        </div>
        
        <div class="bg-white dark:bg-gray-900 shadow-sm rounded-xl border border-gray-200 dark:border-gray-700">
            {{ $this->table }}
        </div>
    </div>
</x-filament-panels::page>