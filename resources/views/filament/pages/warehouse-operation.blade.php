<x-filament-panels::page>
    <form wire:submit="submit">
        {{ $this->form }}

        <div class="mt-4">
            <x-filament::button type="submit" color="{{ match($this->operationType) { 'carico' => 'success', 'scarico' => 'danger', 'reso' => 'warning' } }}">
                @switch($this->operationType)
                    @case('carico') Registra Carico @break
                    @case('scarico') Registra Scarico @break
                    @case('reso') Registra Reso @break
                @endswitch
            </x-filament::button>
        </div>
    </form>
</x-filament-panels::page>
