<?php

namespace App\Livewire;

use Illuminate\Support\Facades\Cookie;
use Livewire\Component;

class CookieNotice extends Component
{
    public bool $showNotice = false;

    public function mount(): void
    {
        // Check if user has already acknowledged the notice
        $this->showNotice = ! Cookie::has('cookie_notice_acknowledged');
    }

    public function acknowledge(): void
    {
        // Set cookie for 1 year
        Cookie::queue('cookie_notice_acknowledged', 'true', 60 * 24 * 365);
        $this->showNotice = false;
    }

    public function render()
    {
        return view('livewire.cookie-notice');
    }
}
