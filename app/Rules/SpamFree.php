<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use OpenAI\Laravel\Facades\OpenAI;

class SpamFree implements ValidationRule
{
    /**
     * Run the validation rule.
     *
     * @param  \Closure(string, ?string=): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $context = 'You are a forum moderator designed to output JSON';

        $prompt = <<<EOT
            Please check if the following comment is spam:
            {$value}
            Expected Response Example:
            {"is_spam": true|false}
            EOT;

        $messages = [
            ['role' => 'system', 'content' => $context],
            ['role' => 'user', 'content' => $prompt]
        ];
    
        $response = OpenAI::chat()->create([
            'model' => 'gpt-3.5-turbo-1106',
            'messages' => $messages,
            'response_format' => ['type' => 'json_object']
        ])
        ->choices[0]->message->content;
    
        $response = json_decode($response);
    
        if ($response->is_spam) {
            $fail('Spam was detected.');
        }
    }
}
