<div class="inline-block">
    <a
        href="{{ route('notifications') }}"
        class="relative rounded-xl border border-gray-200/70 bg-white p-2 text-slate-500 shadow-sm hover:bg-slate-50 hover:shadow-md transition-all duration-200 group inline-block"
        aria-label="Notifications"
        wire:poll.15s
    >
        <svg class="h-6 w-6 transition-transform duration-200 group-hover:scale-110 {{ $this->unreadCount > 0 ? 'animate-[wiggle_1s_ease-in-out_infinite]' : '' }}" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <path d="M18 8a6 6 0 0 0-12 0c0 7-3 9-3 9h18s-3-2-3-9" />
            <path d="M13.73 21a2 2 0 0 1-3.46 0" />
        </svg>
        @if ($this->unreadCount > 0)
            <span class="absolute -right-1 -top-1 min-w-[18px] rounded-full bg-gradient-to-br from-orange-500 to-red-500 px-1.5 py-0.5 text-[10px] font-bold text-white ring-2 ring-white shadow-lg animate-pulse">
                {{ $this->unreadCount > 99 ? '99+' : $this->unreadCount }}
            </span>
        @endif
    </a>

    <style>
    @keyframes wiggle {
        0%, 100% { transform: rotate(0deg); }
        25% { transform: rotate(-10deg); }
        75% { transform: rotate(10deg); }
    }
    </style>
</div>
