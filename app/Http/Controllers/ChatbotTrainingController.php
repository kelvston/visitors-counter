<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;
use JsonException;
use RuntimeException;
use Symfony\Component\Process\Process;

class ChatbotTrainingController extends Controller
{
    private const INTENTS = [
        'greeting',
        'help',
        'unsupported',
        'total_visits_by_gender_in_month',
        'total_visits_by_gender_in_period',
    ];

    public function index(): View
    {
        $data = $this->loadTrainingData();

        return view('chatbot.training', [
            'exampleCount' => count($data['texts']),
            'recentExamples' => array_slice(array_map(
                static fn (string $text, array $annotation): array => [
                    'text' => $text,
                    'intent' => $annotation['intent'],
                ],
                $data['texts'],
                $data['annotations']
            ), -10),
            'intents' => self::INTENTS,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'text' => ['required', 'string', 'min:3', 'max:500'],
            'intent' => ['required', 'string', 'in:'.implode(',', self::INTENTS)],
            'gender_entity' => ['nullable', 'string', 'max:40'],
            'period_entity' => ['nullable', 'string', 'max:80'],
            'month_entity' => ['nullable', 'string', 'max:30'],
        ]);

        $text = trim($validated['text']);
        $entities = array_filter([
            'gender' => trim($validated['gender_entity'] ?? ''),
            'period' => trim($validated['period_entity'] ?? ''),
            'month' => trim($validated['month_entity'] ?? ''),
        ], static fn (string $value): bool => $value !== '');

        foreach ($entities as $value) {
            if (! str_contains(mb_strtolower($text), mb_strtolower($value))) {
                return back()
                    ->withInput()
                    ->withErrors(['entities' => "The annotated entity '{$value}' must appear in the example text."]);
            }
        }

        try {
            $data = $this->loadTrainingData();
            $data['texts'][] = $text;
            $data['annotations'][] = [
                'intent' => $validated['intent'],
                'entities' => $entities,
            ];
            $this->saveTrainingData($data);
        } catch (JsonException|RuntimeException $exception) {
            Log::error('Unable to save chatbot training example.', ['message' => $exception->getMessage()]);

            return back()->withInput()->withErrors([
                'training' => 'Training data could not be saved. Check the application storage permissions and existing training file.',
            ]);
        }

        return back()->with('status', 'Training example added. Retrain the model to apply it.');
    }

    public function train(): RedirectResponse
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

        try {
            $process->mustRun();
        } catch (\Symfony\Component\Process\Exception\ProcessFailedException|\Symfony\Component\Process\Exception\ProcessTimedOutException $exception) {
            Log::error('Chatbot model training from the admin page failed.', [
                'message' => $exception->getMessage(),
                'stderr' => $process->getErrorOutput(),
            ]);

            return back()->withErrors([
                'training' => 'Model training failed. Check the application log and confirm the Python NLP dependencies are installed.',
            ]);
        }

        return back()->with('status', 'The chatbot model has been trained with the latest examples.');
    }

    private function loadTrainingData(): array
    {
        $path = config('services.chatbot.training_data_path');

        if (! File::exists($path)) {
            return ['texts' => [], 'annotations' => []];
        }

        $data = json_decode(File::get($path), true, 512, JSON_THROW_ON_ERROR);
        if (! is_array($data['texts'] ?? null) || ! is_array($data['annotations'] ?? null)
            || count($data['texts']) !== count($data['annotations'])) {
            throw new RuntimeException('The chatbot training data file has an invalid structure.');
        }

        return $data;
    }

    private function saveTrainingData(array $data): void
    {
        $path = config('services.chatbot.training_data_path');
        File::ensureDirectoryExists(dirname($path));

        $handle = fopen($path, 'c+');
        if ($handle === false) {
            throw new RuntimeException('Unable to open the chatbot training data file.');
        }

        try {
            if (! flock($handle, LOCK_EX)) {
                throw new RuntimeException('Unable to lock the chatbot training data file.');
            }

            rewind($handle);
            $existing = stream_get_contents($handle);
            $current = $existing === '' ? ['texts' => [], 'annotations' => []]
                : json_decode($existing, true, 512, JSON_THROW_ON_ERROR);

            if (! is_array($current['texts'] ?? null) || ! is_array($current['annotations'] ?? null)
                || count($current['texts']) !== count($current['annotations'])) {
                throw new RuntimeException('The chatbot training data file has an invalid structure.');
            }

            $current['texts'][] = $data['texts'][array_key_last($data['texts'])];
            $current['annotations'][] = $data['annotations'][array_key_last($data['annotations'])];

            rewind($handle);
            ftruncate($handle, 0);
            fwrite($handle, json_encode($current, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR));
            fflush($handle);
            flock($handle, LOCK_UN);
        } finally {
            fclose($handle);
        }
    }
}
