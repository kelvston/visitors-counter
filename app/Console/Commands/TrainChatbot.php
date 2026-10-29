<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Symfony\Component\Process\Process;

class TrainChatbot extends Command
{
    protected $signature = 'chatbot:train';

    protected $description = 'Train the chatbot NLP model from the intent and entity examples';

    public function handle(): int
    {
        $process = new Process(
            [config('services.chatbot.python_binary'), base_path('nlp/train_chatbot.py')],
            base_path(),
            [
                'CHATBOT_NLP_MODEL_PATH' => config('services.chatbot.model_path'),
                'CHATBOT_TRAINING_DATA_PATH' => config('services.chatbot.training_data_path'),
            ]
        );
        $process->setTimeout(300);
        $process->run(function (string $type, string $output): void {
            $this->output->write($output);
        });

        if (! $process->isSuccessful()) {
            $this->error('Chatbot model training failed.');
            $this->line($process->getErrorOutput());

            return self::FAILURE;
        }

        $this->info('Chatbot NLP model trained successfully.');

        return self::SUCCESS;
    }
}
