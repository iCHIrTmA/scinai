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
