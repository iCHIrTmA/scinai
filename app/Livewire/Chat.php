<?php

namespace App\Livewire;

use Livewire\Attributes\Validate;
use Livewire\Component;

class Chat extends Component
{
    #[Validate('required|max:1000')]
    public string $body = '';

    public array $messages = [];

    // public string $aiContext = 'You are a Justice of the Supreme Court of the Philippines who is also a professor of Environmental Law. You make clear and concise explanations that any person can understand.';

    public function mount()
    {
        // $this->messages[] = ['role' => 'assistant', 'content' => $this->aiContext];
    }

    public function send()
    {
        $this->validate();

        $this->messages[] = ['role' => 'user', 'content' => $this->body];
        $this->messages[] = ['role' => 'assistant', 'content' => ''];

        $this->body = '';
    }

    public function render()
    {
        return view('livewire.chat');
    }
}
