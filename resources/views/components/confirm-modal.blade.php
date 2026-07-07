<div
  x-data="{
    open: false,
    title: '',
    message: '',
    confirmLabel: 'Hapus',
    variant: 'danger',
    action: null,
    params: [],

    show(detail) {
      this.title        = detail.title        ?? 'Konfirmasi';
      this.message      = detail.message      ?? 'Apakah kamu yakin?';
      this.confirmLabel = detail.confirmLabel ?? 'Hapus';
      this.variant      = detail.variant      ?? 'danger';
      this.action       = detail.action       ?? null;
      this.params       = detail.params       ?? [];
      this.open         = true;
    },

    confirm() {
      if (this.action) {
        $dispatch('confirm-modal:confirmed', {
          action: this.action,
          params: this.params
        });
      }
      this.open = false;
    },

    cancel() {
      this.open = false;
    }
  }"
  x-on:confirm-modal:show.window="show($event.detail)"
  x-on:keydown.escape.window="cancel()"
  x-show="open"
  x-cloak
  style="display:none"
>
  {{-- Backdrop --}}
  <div
    class="fixed inset-0 z-50 bg-black/50 backdrop-blur-sm"
    x-show="open"
    x-transition:enter="transition ease-out duration-200"
    x-transition:enter-start="opacity-0"
    x-transition:enter-end="opacity-100"
    x-transition:leave="transition ease-in duration-150"
    x-transition:leave-start="opacity-100"
    x-transition:leave-end="opacity-0"
    @click="cancel()"
  ></div>

  {{-- Modal panel --}}
  <div
    class="fixed inset-0 z-50 flex items-center justify-center p-4"
    x-show="open"
    x-transition:enter="transition ease-out duration-200"
    x-transition:enter-start="opacity-0 scale-95"
    x-transition:enter-end="opacity-100 scale-100"
    x-transition:leave="transition ease-in duration-150"
    x-transition:leave-start="opacity-100 scale-100"
    x-transition:leave-end="opacity-0 scale-95"
  >
    <div
      class="relative w-full max-w-sm bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800 rounded-md shadow-lg p-6"
      @click.stop
    >
      {{-- Icon --}}
      <div class="flex items-start gap-4">
        <div
          class="flex-shrink-0 flex items-center justify-center w-10 h-10 rounded-md"
          :class="{
            'bg-red-50 dark:bg-red-500/10': variant === 'danger',
            'bg-amber-50 dark:bg-amber-500/10': variant === 'warning'
          }"
        >
          {{-- Danger icon --}}
          <svg
            x-show="variant === 'danger'"
            class="w-5 h-5 text-red-500"
            fill="none" stroke="currentColor" stroke-width="1.75" viewBox="0 0 24 24"
          >
            <path stroke-linecap="round" stroke-linejoin="round"
              d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948
                 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949
                 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697
                 16.126zM12 15.75h.007v.008H12v-.008z"/>
          </svg>
          {{-- Warning icon --}}
          <svg
            x-show="variant === 'warning'"
            class="w-5 h-5 text-amber-500"
            fill="none" stroke="currentColor" stroke-width="1.75" viewBox="0 0 24 24"
          >
            <path stroke-linecap="round" stroke-linejoin="round"
              d="M9.879 7.519c1.171-1.025 3.071-1.025 4.242
                 0 1.172 1.025 1.172 2.687 0 3.712-.203.179-.43.326-.67.442-.745.361-1.45.999-1.45
                 1.827v.75M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-9 5.25h.008v.008H12v-.008z"/>
          </svg>
        </div>

        <div class="flex-1 min-w-0">
          {{-- Title --}}
          <h3
            class="text-sm font-semibold text-gray-900 dark:text-white mb-1"
            x-text="title"
          ></h3>
          {{-- Message --}}
          <p
            class="text-sm text-gray-500 dark:text-gray-400 leading-relaxed"
            x-text="message"
          ></p>
        </div>
      </div>

      {{-- Actions --}}
      <div class="flex items-center justify-end gap-2 mt-6">
        {{-- Cancel --}}
        <button
          type="button"
          @click="cancel()"
          class="px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-300
                 bg-white dark:bg-gray-800
                 border border-gray-300 dark:border-gray-700
                 rounded-md hover:bg-gray-50 dark:hover:bg-gray-700
                 transition-colors duration-150"
        >
          Batal
        </button>

        {{-- Confirm --}}
        <button
          type="button"
          @click="confirm()"
          class="px-4 py-2 text-sm font-medium text-white rounded-md
                 transition-colors duration-150"
          :class="{
            'bg-red-500 hover:bg-red-600': variant === 'danger',
            'bg-amber-500 hover:bg-amber-600': variant === 'warning'
          }"
          x-text="confirmLabel"
        ></button>
      </div>
    </div>
  </div>
</div>
