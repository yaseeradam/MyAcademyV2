<?php

namespace App\Livewire;

use Livewire\Component;

class GlobalModal extends Component
{
    public $show = false;
    public $type = 'success'; // success, error, warning, info
    public $title = '';
    public $message = '';

    protected $listeners = ['showModal'];

    public function showModal($type, $title, $message)
    {
        $this->type = $type;
        $this->title = $title;
        $this->message = $message;
        $this->show = true;
    }

    public function close()
    {
        $this->show = false;
    }

    public function render()
    {
        return view('livewire.global-modal');
    }
}
