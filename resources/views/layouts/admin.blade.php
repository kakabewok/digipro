<x-layouts::admin.sidebar :title="$title ?? null">
    <flux:main>
        {{ $slot }}
    </flux:main>
    <x-confirm-modal />
</x-layouts::admin.sidebar>
