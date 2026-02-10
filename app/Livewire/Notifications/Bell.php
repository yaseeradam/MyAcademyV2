<?php

namespace App\Livewire\Notifications;

use App\Models\InAppNotification;
use Livewire\Attributes\Computed;
use Livewire\Component;

class Bell extends Component
{
    #[Computed]
    public function unreadCount(): int
    {
        $user = auth()->user();
        if (! $user) {
            return 0;
        }

        return (int) InAppNotification::query()
            ->where('user_id', $user->id)
            ->whereNull('read_at')
            ->count();
    }

    public function render()
    {
        return view('livewire.notifications.bell');
    }
}
