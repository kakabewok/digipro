@props(['status'])

@php
$config = match($status) {
    'paid'      => ['label' => 'Paid',       'class' => 'bg-green-50 text-green-600 border-green-200 dark:bg-green-500/10 dark:text-green-400 dark:border-green-500/20'],
    'pending'   => ['label' => 'Pending',    'class' => 'bg-amber-50 text-amber-600 border-amber-200 dark:bg-amber-500/10 dark:text-amber-400 dark:border-amber-500/20'],
    'expired'   => ['label' => 'Expired',    'class' => 'bg-gray-100 text-gray-500 border-gray-200 dark:bg-gray-700/40 dark:text-gray-400 dark:border-gray-600'],
    'cancelled' => ['label' => 'Cancelled',  'class' => 'bg-red-50 text-red-500 border-red-200 dark:bg-red-500/10 dark:text-red-400 dark:border-red-500/20'],
    'completed' => ['label' => 'Completed',  'class' => 'bg-green-50 text-green-600 border-green-200 dark:bg-green-500/10 dark:text-green-400 dark:border-green-500/20'],
    'processing'=> ['label' => 'Processing', 'class' => 'bg-blue-50 text-blue-600 border-blue-200 dark:bg-blue-500/10 dark:text-blue-400 dark:border-blue-500/20'],
    'active'    => ['label' => 'Aktif',      'class' => 'bg-green-50 text-green-600 border-green-200 dark:bg-green-500/10 dark:text-green-400 dark:border-green-500/20'],
    'inactive'  => ['label' => 'Nonaktif',   'class' => 'bg-gray-100 text-gray-500 border-gray-200 dark:bg-gray-700/40 dark:text-gray-400 dark:border-gray-600'],
    default     => ['label' => ucfirst($status), 'class' => 'bg-gray-100 text-gray-500 border-gray-200 dark:bg-gray-700/40 dark:text-gray-400 dark:border-gray-600'],
};
@endphp

<span class="inline-flex items-center gap-1.5 px-2 py-0.5 rounded-md text-xs font-semibold border {{ $config['class'] }}">
    <span class="w-1.5 h-1.5 rounded-full
        @if($status === 'paid' || $status === 'completed') bg-green-500 dark:bg-green-400
        @elseif($status === 'pending') bg-amber-500 dark:bg-amber-400
        @elseif($status === 'processing') bg-blue-500 dark:bg-blue-400
        @elseif($status === 'expired') bg-gray-400 dark:bg-gray-500
        @elseif($status === 'cancelled') bg-red-500 dark:bg-red-400
        @elseif($status === 'active') bg-green-500 dark:bg-green-400
        @elseif($status === 'inactive') bg-gray-400 dark:bg-gray-500
        @else bg-gray-400
        @endif
    "></span>
    {{ $config['label'] }}
</span>
