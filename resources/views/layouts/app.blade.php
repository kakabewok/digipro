<x-layouts::app.sidebar :title="$title ?? null">
    <flux:main>
        {{ $slot }}
    </flux:main>
    <x-confirm-modal />
</x-layouts::app.sidebar>
