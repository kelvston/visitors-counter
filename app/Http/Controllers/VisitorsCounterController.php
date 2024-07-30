<?php

namespace App\Http\Controllers;
use App\Models\Counter;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;

class VisitorsCounterController extends Controller
{


    public function show()
    {
        $user = auth()->user();
        $timePeriods = [
            'yesterday' => Carbon::yesterday()->format('Y-m-d'),
            'today' => Carbon::today()->format('Y-m-d'),
            'startOfWeek' => Carbon::now()->startOfWeek()->format('Y-m-d'),
            'startOfMonth' => Carbon::now()->startOfMonth(),
            'lastMonthStart' => Carbon::now()->subMonth()->startOfMonth()->format('Y-m-d'),
            'threeMonthsAgo' => Carbon::now()->subMonths(3)->format('Y-m-d')
        ];

        $summary = [];

        foreach (['1' => 'Male', '2' => 'Female', '3' => 'Other'] as $gender => $label) {
            foreach (['yesterday', 'today', 'weekly', 'monthly', 'lastMonth', 'threeMonths', 'total'] as $period) {
                $query = Counter::where('user_id', $user->id)
                    ->where('gender', $gender);

                if ($period === 'weekly') {
                    $query->whereBetween('created_at', [$timePeriods['startOfWeek'], $timePeriods['today']]);
                } elseif ($period === 'monthly') {
                    $query->whereBetween('created_at', [$timePeriods['startOfMonth']->format('Y-m-d'), $timePeriods['today']]);
                } elseif ($period === 'lastMonth') {
                    $query->whereBetween('created_at', [$timePeriods['lastMonthStart'], $timePeriods['startOfMonth']->copy()->subDay()->format('Y-m-d')]);
                } elseif ($period === 'threeMonths') {
                    $query->whereBetween('created_at', [$timePeriods['threeMonthsAgo'], $timePeriods['today']]);
                } elseif ($period === 'total') {
                    // No additional date filtering for 'total'
                } else {
                    // Default to 'yesterday' or 'today'
                    $query->whereDate('created_at', $timePeriods[$period]);
                }

                $summary[$period][$label] = $query->sum('counts') ?? 0;
            }
        }

        // Calculate totalVisits
        foreach (['yesterday', 'today', 'weekly', 'monthly', 'lastMonth', 'threeMonths', 'total'] as $period) {
            $totalVisits = 0;
            foreach (['Male', 'Female', 'Other'] as $label) {
                $totalVisits += $summary[$period][$label];
            }
            $summary[$period]['totalVisits'] = $totalVisits;
        }

        return view('counter', [
            'summary' => $summary,
        ]);
    }



    public function increment(Request $request)
    {
        $user = Auth::user();
        $counter = Counter::firstOrCreate(
            [
                'user_id' => $user->id,
                'gender' => $request['gender'],
                'created_at' => Carbon::today()
            ],
            ['counts' => 0]
        );
        $counter->increment('counts');

        if ($request->ajax()) {
            \Log::info('AJAX request detected.');
            return response()->json(['success' => true]);
        }

        \Log::info('Non-AJAX request detected.');
        return redirect()->route('counter.report');
    }


    public function getVisitorSummary(Request $request)
    {

        $summary = [];

        // Default summary
        $summary['today'] = $this->getVisitorData(Carbon::today(), Carbon::today()->endOfDay());
        $summary['yesterday'] = $this->getVisitorData(Carbon::yesterday(), Carbon::yesterday()->endOfDay());
        $summary['weekly'] = $this->getVisitorData(Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek());
        $summary['monthly'] = $this->getVisitorData(Carbon::now()->startOfMonth(), Carbon::now()->endOfMonth());
        $summary['lastMonth'] = $this->getVisitorData(Carbon::now()->subMonth()->startOfMonth(), Carbon::now()->subMonth()->endOfMonth());
        $summary['threeMonths'] = $this->getVisitorData(Carbon::now()->subMonths(3)->startOfMonth(), Carbon::now()->endOfMonth());
        $summary['total'] = $this->getVisitorData(null, null);

        // Check for date range filter
        if ($request->has('startDate') && $request->has('endDate')) {
            $startDate = Carbon::createFromFormat('Y-m-d', $request->input('startDate'))->startOfDay();
            $endDate = Carbon::createFromFormat('Y-m-d', $request->input('endDate'))->endOfDay();
            $summary['filtered'] = $this->getVisitorData($startDate, $endDate);
        }

        return $summary;
    }
    public function getVisitorChartSummary(Request $request)
    {

        $summary = [];

        // Default summary
        $summary['today'] = $this->getVisitorData(Carbon::today(), Carbon::today()->endOfDay());
        $summary['yesterday'] = $this->getVisitorData(Carbon::yesterday(), Carbon::yesterday()->endOfDay());
        $summary['weekly'] = $this->getVisitorData(Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek());
        $summary['monthly'] = $this->getVisitorData(Carbon::now()->startOfMonth(), Carbon::now()->endOfMonth());
        $summary['lastMonth'] = $this->getVisitorData(Carbon::now()->subMonth()->startOfMonth(), Carbon::now()->subMonth()->endOfMonth());
        $summary['threeMonths'] = $this->getVisitorData(Carbon::now()->subMonths(3)->startOfMonth(), Carbon::now()->endOfMonth());
        $summary['total'] = $this->getVisitorData(null, null);

        // Check for date range filter
        if ($request->has('startDate') && $request->has('endDate')) {
            $startDate = Carbon::createFromFormat('Y-m-d', $request->input('startDate'))->startOfDay();
            $endDate = Carbon::createFromFormat('Y-m-d', $request->input('endDate'))->endOfDay();
            $summary['filtered'] = $this->getVisitorData($startDate, $endDate);
        }

        return $summary;
    }

    public function fetchVisitorSummary($period, $specificDate = null)
    {
        $request = new Request(['period' => $period, 'specificDate' => $specificDate]);
        $summary = $this->getVisitorChartSummary($request);

        if ($specificDate) {
            // Fetch the specific date summary
            $summary['specificDate'] = $this->getVisitorData(Carbon::parse($specificDate)->startOfDay(), Carbon::parse($specificDate)->endOfDay());
        }

        return $summary[$period] ?? [];
    }

    private function analyzeTextWithSpacy(Request $request)
    {
        $userMessage = $request->input('message');
        $pythonScript = 'C:/xampp/htdocs/visitor-counter/analyze_text_spacy.py';
        $pythonExecutable = 'C:/Users/yoga/AppData/Local/Programs/Python/Python312/python.exe';

        $command = escapeshellcmd("{$pythonExecutable} {$pythonScript} \"{$userMessage}\"");

        $output = shell_exec($command);

        if ($output === null || $output === '') {
            return ['error' => 'Error executing Python script. No output returned.'];
        }

        $response = json_decode($output, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            return ['error' => 'Error decoding JSON response from Python script.'];
        }

        return $response;
    }




    public function testPythonScript()
    {
        // Path to your Python script
        $scriptPath = base_path('analyze_text_spacy.py');

        // Full path to the Python executable
        $pythonPath = 'C:\\Users\\yoga\\AppData\\Local\\Programs\\Python\\Python312\\python.exe';

        // Command to execute the Python script
        $command = "$pythonPath $scriptPath 'Hello World!'";

        // Execute the command
        $output = shell_exec($command);
        // Return the output as JSON
        return response()->json(['output' => $output]);
    }


    public function respond(Request $request)
    {
        $userMessage = strtolower(trim($request->input('message')));

        // Perform SpaCy analysis
        $analysis = $this->analyzeTextWithSpacy($request);

        // Debugging output

        // Check for errors
        if (isset($analysis['error'])) {
            return response()->json(['message' => $analysis['error']]);
        }

        // Extract intent and entities
        $intent = $analysis['intent'] ?? 'unknown';
        $entities = $analysis['entities'] ?? [];

        // Define responses based on intents
        $responses = [
            'weather' => 'Here is the weather information you requested.',
            'visitor' => 'Here is the visitor data you asked for.',
            'gender' => 'Here is the data for different genders.',
            'unknown' => 'I am not sure about that. Can you please clarify?'
        ];

        // Handle responses based on the recognized intent
        if ($intent === 'weather') {
            $date = 'today'; // Default to today
            foreach ($entities as $entity) {
                if ($entity[1] === 'DATE') {
                    $date = $entity[0]; // Extract date if provided
                }
            }
            // Fetch weather data based on the date
            // Implement your logic to get weather data here
            return response()->json(['message' => "Here's the weather information for {$date}."]);
        }

        if ($intent === 'visitor') {
            // Handle visitor data request
            $summary = $this->fetchVisitorSummary($userMessage);
            $totalVisits = ($summary['Male'] ?? 0) + ($summary['Female'] ?? 0) + ($summary['Other'] ?? 0);
            return response()->json(['message' => 'Here is the visitor data you requested.', 'totalVisits' => $totalVisits]);
        }

        if ($intent === 'gender') {
            // Handle gender-specific queries
            $gender = 'male'; // Default to male
            foreach ($entities as $entity) {
                if (in_array($entity[0], ['male', 'female', 'other'])) {
                    $gender = $entity[0];
                }
            }
            $summary = $this->getVisitorData(null, null); // Adjust as needed
            $totalVisits = $summary[$gender] ?? 0;
            return response()->json(['message' => "Total visits for {$gender}:", 'totalVisits' => $totalVisits]);
        }

        // Default response if no intent matches
        return response()->json(['message' => "I'm sorry, I didn't understand that."]);
    }




    // Recognize intent using pattern matching or NLP
    protected function recognizeIntent($message)
    {
        // Implement NLP or pattern matching to determine the intent
        // For example:
        $intents = [
            'hello' => 'hello',
            'hi' => 'hello',
            'help' => 'help',
            'today' => 'today',
            'yesterday' => 'yesterday',
            'weekly' => 'weekly',
            'monthly' => 'monthly',
            'last month' => 'last month',
            'three months' => 'three months',
            'male' => 'male',
            'female' => 'female',
            'other' => 'other',
            'analyze' => 'analyze',
            // Add more patterns here...
        ];

        foreach ($intents as $keyword => $intent) {
            if (strpos($message, $keyword) !== false) {
                return $intent;
            }
        }

        return 'unknown';
    }


    private function getVisitorData($startDate = null, $endDate = null)
    {

        $query = Counter::query();

        if ($startDate && $endDate) {
            $query->whereBetween('created_at', [$startDate, $endDate]);
        }

        return [
            'Male' => $query->where('gender', 1)->count(),
            'Female' => $query->where('gender', 2)->count(),
            'Other' => $query->where('gender', 3)->count(),
            'totalVisits' => $query->count(),
        ];
    }




public function index()
{
    return view('chatbot.index');
}

public function getVisitorDataForChatbot(Request $request)
{
    $period = $request->input('period');

    $timePeriods = [
        'yesterday' => Carbon::yesterday(),
        'today' => Carbon::today(),
        'weekly' => Carbon::now()->startOfWeek(),
        'monthly' => Carbon::now()->startOfMonth(),
        'lastMonth' => Carbon::now()->subMonth()->startOfMonth(),
        'threeMonths' => Carbon::now()->subMonths(3)->startOfMonth(),
        'total' => null
    ];

    $startDate = $timePeriods[$period] ?? null;
    $endDate = ($period === 'total') ? null : $timePeriods['today']->endOfDay();

    $visitorData = $this->getVisitorData($startDate, $endDate);

    return response()->json(['summary' => [$period => $visitorData]]);
}

}
