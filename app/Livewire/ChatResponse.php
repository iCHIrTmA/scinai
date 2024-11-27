<?php

namespace App\Livewire;

use App\AI\EnviLawProfessor;
use Livewire\Component;

class ChatResponse extends Component
{
    public array $prompt = [];
    public array $messages = [];
    public string $response = '';

    public function mount()
    {
        $this->getResponse();
    }

    public function getResponse()
    {
        $response = (new EnviLawProfessor())
                ->createThread()
                ->send();
                
        $this->response = collect($response->data)->first()->content[0]->text->value;
    }

    public function render()
    {
        return view('livewire.chat-response');
    }
}
