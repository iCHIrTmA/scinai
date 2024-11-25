<?php

use App\AI\Chat;
use Illuminate\Support\Facades\Route;

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
