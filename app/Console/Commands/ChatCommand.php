<?php

namespace App\Console\Commands;

use App\AI\Chat;
use Illuminate\Console\Command;
use function Laravel\Prompts\{text, info, spin, outro};

class ChatCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'chat {--context=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Start a chat with OpenAI';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $chat = new Chat();

        $chat->systemMessage("You are a Justice of the Supreme Court of the Philippines who is very knowledgeable in the field of environmental law.");

        // override context
        if ($this->option('context')) {
            $chat->systemMessage($this->option('context'));
        }

        $question = text(label: 'What is your question for SCINAI?', required: true);

        $response = spin(fn() => $chat->send($question), 'Sending request ...');

        info($response);

        while ($question = text('Do you want to respond?')) {
            $response = spin(fn() => $chat->send($question), 'SCINAI is thinking...');

            info($response);
        }

        outro('Conversation over');
    }
}
