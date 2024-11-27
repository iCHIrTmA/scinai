<?php

use App\AI\Chat;
use App\AI\EnviLawProfessor;
use App\Livewire\Chat as LivewireChat;
use App\Rules\SpamFree;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;
use OpenAI\Laravel\Facades\OpenAI;

Route::get('/', LivewireChat::class);

// Route::get('/', function () {
//     $chat = new Chat();

//     $poem = $chat
//         ->systemMessage("You are a Justice of the Supreme Court of the Philippines who is very knowledgeable in the field of environmental law and also skilled in poetry")
//         ->send("Please write a poem on the constitutional right of the Filipino people to a balanced and healthful ecology.");


//     $poem = $chat->reply("Good, can you make it more readable for children");
    
//     return view('welcome', ['poem' => $poem]);
// });

Route::get('/case-summary', function () {
    return view('case-summary');
});

Route::post('/summarize', function () {
    $attributes = request()->validate([
        'topic' => ['required', 'string', 'min:16', 'max:256']
    ]);

    $prompt = "Please make a readable case summary about {$attributes['topic']}
        with citations of environmental laws violated and rules of environmental procedure applied.
        First, make a short summary of the facts,
        then relevant issues and
        lastly the Supreme Court ruling of the issues";

    $chat = new Chat();

    $response = $chat->send($prompt);

    dd($response);
});


Route::get('/check-spam', function () {
    return view('check-spam');
});

Route::post('/check-spam', function() {

    request()->validate([
        'body' => ['required', 'string', 'min:2', 'max:256', new SpamFree()]
    ]);

    return "Redirect wherever is valid. Text made in good faith :)";
});

Route::get('/environmental-law', function() {

    $professor = new EnviLawProfessor();

    $messages = $professor->createThread()
                ->write('Hello')
                ->write('How to make a petition for writ of kalikasan? What are the requirements?')
                ->send();

    dd($messages, __METHOD__);
});
