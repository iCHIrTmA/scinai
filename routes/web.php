<?php

use App\AI\Chat;
use App\Rules\SpamFree;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;
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

    request()->validate([
        'body' => ['required', 'string', 'min:2', 'max:256', new SpamFree()]
    ]);

    return "Redirect wherever is valid. Text made in good faith :)";
});

Route::get('/environmental-law', function() {

    // Upload files
    // $files = [];
    // foreach (Storage::disk('public')->files('envi-law') as $filePath) {
    //     $files[] = OpenAI::files()->upload([
    //         'purpose' => 'assistants',
    //         'file' => fopen(storage_path("app/public/$filePath"), 'rb')
    //     ]);
    // }


    // Delete uploaded files
    // $files = OpenAI::files()->list();
    // $deleteStatus = [];
    // foreach (collect($files->data)->pluck('id') as $fileId) {
    //     $deleteStatus[] = OpenAI::files()->delete($fileId);
    // }

    // dd($deleteStatus, __METHOD__);

    // dd(fopen(storage_path('app/public/envi-law/Complete-List-of-all-Environmental-Laws-and-Policies-in-the-Philippines-GreenDev-Solutions.pdf'), 'rb'));

    // OpenAI::files()->upload()

    // $files = OpenAI::files()->list();


    // dd($files, __METHOD__);

    // create vector / training data
    // $vector = OpenAI::vectorStores()->create([
    //     'name' => 'Envi Law Training Data',
    //     'file_ids' => collect($files->data)->pluck('id')
    // ]);

    $vectorList = OpenAI::vectorStores()->list();
    $vectorIds = collect($vectorList->data)->pluck('id');

    // dd(collect($vectorIds->data)->pluck('id'), __METHOD__);

    // dd($vectorIds, __METHOD__); 

    $aiContext = 'You are a Justice of the Supreme Court of the Philippines who is also a professor of Environmental Law.
            You make clear and concise explanations that any person can understand.';

    $assistant = OpenAI::assistants()->create([
        'model' => 'gpt-4o-mini',
        'name' => 'Envi Law Professor',
        'instructions' => $aiContext,
        'tools' => [
            ['type' => 'file_search']
        ],
        'tool_resources' => [
            'file_search' => [
                'vector_store_ids' => [...$vectorIds]
            ],
        ]
    ]);

    $run = OpenAI::threads()->createAndRun([
        'assistant_id' => $assistant->id,
        'thread' => [
            'messages' => [
                [
                    'role' => 'user',
                    'content' => 'When to petition for writ of kalikasan?'
                ]
            ]
        ],
    ]);

    do {
        sleep(1);

        $run = OpenAI::threads()->runs()->retrieve(
            threadId: $run->threadId,
            runId: $run->id
        );
    } while ($run->status !== 'completed');

    $messages = OpenAI::threads()->messages()->list($run->threadId);

    dd($messages, __METHOD__);
});
