<?php

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    $poem = Http::withToken(config('services.openai.secret'))
        ->post('https://api.openai.com/v1/chat/completions',
            [
                "model" => "gpt-3.5-turbo",
                "messages" => [
                    [
                        "role" => "system",
                        "content" => "You are a helpful assistant."
                    ],
                    [
                        "role" => "user",
                        "content" => "Write a haiku that explains the concept of recursion."
                    ]
                ]
            ]
        )->json('choices.0.message.content');

    return view('welcome', ['poem' => $poem]);
});
