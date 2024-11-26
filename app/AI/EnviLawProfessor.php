<?php

namespace App\AI;

use Illuminate\Support\Facades\Storage;
use OpenAI\Laravel\Facades\OpenAI;
use OpenAI\Responses\Assistants\AssistantResponse;
use OpenAI\Responses\Threads\Messages\ThreadMessageListResponse;
use OpenAI\Responses\Threads\Messages\ThreadMessageResponse;

class EnviLawProfessor
{
    protected AssistantResponse $assistant;
    protected string $threadId;

    public function __construct(?string $id = null)
    {
        $assistantId = config('services.openai.assistant_id');

        if ($id) {
            $assistantId = $id;
        }

        $this->assistant = OpenAI::assistants()->retrieve($assistantId);

        return $this->assistant;
    }

    public static function create(array $overrideConfig = []): static
    {
        $aiContext = 'You are a Justice of the Supreme Court of the Philippines who is also a professor of Environmental Law.
            You make clear and concise explanations that any person can understand.';

        $config = [
            'model' => 'gpt-4o-mini',
            'name' => 'Envi Law Professor',
            'instructions' => $aiContext,
            'tools' => [
                ['type' => 'file_search']
            ]
            // 'tool_resources' => [
            //     'file_search' => [
            //         'vector_store_ids' => [...$vectorIds]
            //     ],
            // ]
        ];

        if ($config) {
            $config = array_merge_recursive($config, $overrideConfig);
        }

        $assistant = OpenAI::assistants()->create($config);

        return new static($assistant->id);
    }

    public function study(?string $fileDirectory = 'envi-law', $assistant): AssistantResponse
    {
        // Upload files
        $files = [];
        foreach (Storage::disk('public')->files($fileDirectory) as $filePath) {
            $files[] = OpenAI::files()->upload([
                'purpose' => 'assistants',
                'file' => fopen(storage_path("app/public/$filePath"), 'rb')
            ]);
        }


        // Delete uploaded files
        // $deleteStatus = [];
        // foreach (collect($files->data)->pluck('id') as $fileId) {
        //     $deleteStatus[] = OpenAI::files()->delete($fileId);
        // }


        $files = OpenAI::files()->list();

        // create vector / training data
        $vector = OpenAI::vectorStores()->create([
            'name' => 'Envi Law Training Data',
            'file_ids' => collect($files->data)->pluck('id')
        ]);

        $vectorList = OpenAI::vectorStores()->list();
        $vectorIds = collect($vectorList->data)->pluck('id');

        OpenAI::assistants()
            ->modify(
                id: $assistant->id ?? config('services.openai.assistant_id'),
                parameters: ['tool_resources' => ['file_search' => ['vector_store_ids' => [...$vectorIds]]]]
            );

        return $assistant;
    }

    public function createThread($parameters = []): static
    {
        $thread = OpenAI::threads()->create($parameters);

        $this->threadId = $thread->id;

        return $this;
    }

    public function messages(): ThreadMessageListResponse
    {
        return OpenAI::threads()->messages()->list($this->threadId);
    }

    public function write(string $message): static
    {
        OpenAI::threads()
            ->messages()
            ->create(
                threadId: $this->threadId,
                parameters: ['role' => 'user', 'content' => $message]
            );

        return $this;
    }

    public function send()
    {
        $run = OpenAI::threads()
            ->runs()
            ->create($this->threadId, ['assistant_id' => $this->assistant->id]);
        
        do {
            sleep(1);
    
            $run = OpenAI::threads()->runs()->retrieve(
                threadId: $run->threadId,
                runId: $run->id
            );
        } while ($run->status !== 'completed');

        return $this->messages();
    }
}
