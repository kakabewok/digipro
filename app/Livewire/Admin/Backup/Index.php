<?php
namespace App\Livewire\Admin\Backup;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Storage;
use Livewire\Component;

class Index extends Component
{
    public function runBackup(): void
    {
        Artisan::call('backup:run', ['--only-db' => true]);
        session()->flash('success', 'Backup completed successfully.');
    }

    public function downloadBackup(string $file): \Symfony\Component\HttpFoundation\StreamedResponse
    {
        return Storage::disk('local')->download($file);
    }

    public function deleteBackup(string $file): void
    {
        Storage::disk('local')->delete($file);
        session()->flash('success', 'Backup deleted.');
    }

    public function render()
    {
        $backups = [];
        if (Storage::disk('local')->exists(config('backup.backup.name'))) {
            $files = Storage::disk('local')->files(config('backup.backup.name'));
            foreach ($files as $file) {
                if (substr($file, -4) === '.zip') {
                    $backups[] = [
                        'name' => basename($file),
                        'path' => $file,
                        'size' => number_format(Storage::disk('local')->size($file) / 1048576, 2) . ' MB',
                        'date' => \Carbon\Carbon::createFromTimestamp(Storage::disk('local')->lastModified($file)),
                    ];
                }
            }
        }
        
        // Sort newest first
        usort($backups, fn ($a, $b) => $b['date'] <=> $a['date']);

        return view('livewire.admin.backup.index', [
            'backups' => $backups,
        ])->layout('layouts.admin', ['title' => 'Database Backups']);
    }
}
