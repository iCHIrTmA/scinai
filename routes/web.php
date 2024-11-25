<?php

use App\AI\Chat;
use Illuminate\Support\Facades\Route;
use OpenAI\Laravel\Facades\OpenAI;

Route::get('/', function () {
    $chat = new Chat();

    $poem = $chat
        ->systemMessage("You are a Justice of the Supreme Court of the Philippines who is very knowledgeable in the field of environmental law and also skilled in poetry")
        ->send("Please write a poem on the constitutional right of the Filipino people to a balanced and healthful ecology.");


    $poem = $chat->reply("Good, can you make it more readable for children");
    
    return view('welcome', ['poem' => $poem]);
});

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

    $attributes = request()->validate([
        'body' => ['required', 'string', 'min:16', 'max:256']
    ]);

    $prompt = <<<EOT
        Please check if the following comment is spam:
        {$attributes['body']}
        Expected Response Example:
        {"is_spam": true|false}
        EOT;

    $messages = [
        ['role' => 'system', 'content' => 'You are a forum moderator designed to output JSON'],
        ['role' => 'user', 'content' => $prompt]
    ];

    $response = OpenAI::chat()->create([
        'model' => 'gpt-3.5-turbo-1106',
        'messages' => $messages,
        'response_format' => ['type' => 'json_object']
    ])->choices[0]->message->content;

    $response = json_decode($response);

    return $response->is_spam ? 'THIS IS SPAM' : 'Not spam. Text made in good faith :)';
});
