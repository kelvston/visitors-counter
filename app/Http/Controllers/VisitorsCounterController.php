<?php

namespace App\Http\Controllers;
use App\Models\Counter;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
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
            'startOfWeek' => Carbon::now()->startOfWeek(),
            'startOfMonth' => Carbon::now()->startOfMonth(),
            'lastMonthStart' => Carbon::now()->subMonth()->startOfMonth(),
            'lastMonthEnd' => Carbon::now()->subMonth()->endOfMonth(),
            'threeMonthsAgo' => Carbon::now()->subMonths(3)
        ];

        $summary = [];

        foreach (['1' => 'Male', '2' => 'Female', '3' => 'Other'] as $gender => $label) {
            foreach (['yesterday', 'today', 'weekly', 'monthly', 'lastMonth', 'threeMonths', 'total'] as $period) {
                $query = Counter::where('user_id', $user->id)
                    ->where('gender', $gender);

                switch ($period) {
                    case 'weekly':
                        $query->whereBetween('created_at', [$timePeriods['startOfWeek'], Carbon::now()]);
                        break;
                    case 'monthly':
                        // Using raw Carbon objects without formatting
                        $query->whereBetween('created_at', [$timePeriods['startOfMonth'], Carbon::now()]);
                        break;
                    case 'lastMonth':
                        $query->whereBetween('created_at', [$timePeriods['lastMonthStart'], $timePeriods['lastMonthEnd']]);
                        break;
                    case 'threeMonths':
                        $query->whereBetween('created_at', [$timePeriods['threeMonthsAgo'], Carbon::now()]);
                        break;
                    case 'total':
                        // No additional date filtering for 'total'
                        break;
                    default:
                        // Default to 'yesterday' or 'today'
                        $query->whereDate('created_at', $timePeriods[$period]);
                }

                $summary[$period][$label] = $query->sum('counts') ?? 0;
            }
        }

        // Calculate totalVisits for each period
        foreach (['yesterday', 'today', 'weekly', 'monthly', 'lastMonth', 'threeMonths', 'total'] as $period) {
            $summary[$period]['totalVisits'] = array_sum(array_values($summary[$period]));
        }

//        dd($summary); // Check output after update
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
        $text = $request->input('message');

        // Send the text to the Python endpoint
        $response = Http::post('http://localhost:8080/analyze', [
            'text' => $text
        ]);

        $analysis = json_decode($response->body(), true);

        // Check for errors
        if (isset($analysis['error'])) {
            return response()->json(['message' => $analysis['error']]);
        }

        // Extract entities and tokens
        $entities = $analysis['entities'] ?? [];
        $tokens = $analysis['tokens'] ?? [];

        // Initialize variables
        $date = null;
        $gender = null;
        $specificDay = null;
        $month = null;

        // Identify and parse entities
        foreach ($entities as $entity) {
            if ($entity[1] === 'DATE') {
                // If DATE entity is captured, assume it's the month or specific date
                $month = $this->mapMonth($entity[0]);
            } elseif ($entity[1] === 'GENDER') {
                $gender = $this->mapGender($entity[0]);
            }
        }

        // Extract specific day if present in the text
        $specificDayMatch = preg_match('/(\d{1,2})/', $text, $matches);
        $specificDay = $specificDayMatch ? $matches[1] : null;
        $query = DB::table('counters');



        // If a month was identified and a specific day is provided or not
        if ($month && $specificDay) {
            // Construct the full date with the specific day
            $date = date('Y-' . $month . '-' . str_pad($specificDay, 2, '0', STR_PAD_LEFT));
        } elseif ($month) {
            $startDate = $month ? date('Y-' . $month . '-01') : date('Y-m-01');
            $endDate = date("Y-m-t", strtotime($startDate));

        }

        if ($gender) {

            $query->where('gender', $gender);

            if ($specificDay) {
                // Specific date handling for gender
                $parsedDate = $date ?: date('Y-m-d'); // Convert date if parsed
                $gender_text = $this->mapGenderText($gender);
                dd($gender_text);
                return response()->json(['message' => "Total visits for {$gender} on {$parsedDate}: 0"]);
            } else {
                // General period handling for gender
                $parsedDate = $date ?: date('Y-m'); // Default to current month if not parsed
                $startDate = "$parsedDate-01";
                $endDate = date("Y-m-t", strtotime($startDate));
                return response()->json(['message' => "Total visits for {$gender} from {$startDate} to {$endDate}: 0"]);
            }
        }else{
            if ($specificDay) {
                // Specific date handling for gender
                $parsedDate = $date ?: date('Y-m-d'); // Convert date if parsed
                return response()->json(['message' => "Total visits  on {$parsedDate}: 0"]);
            } else {
                // General period handling for gender
                $parsedDate = $date ?: date('Y-m'); // Default to current month if not parsed
                $startDate = "$parsedDate-01";
                $endDate = date("Y-m-t", strtotime($startDate));
                return response()->json(['message' => "Total visits  from {$startDate} to {$endDate}: 0"]);
            }
        }

        // Default response if no intent matches
        return response()->json(['message' => "I'm sorry, I didn't understand that."]);
    }

    public function mapGender($genderStr)
    {
        $genderMap = [
            'male' => 1,
            'female' => 2,
            'boys'=>1,
            'boy'=>1,
            'girl'=>2,
            'girls'=>2,
            // Add other mappings if necessary
        ];

        return $genderMap[strtolower($genderStr)] ?? null;
    }
    public function mapGenderText($gender)
    {
        $gender = [
           1 =>'male',
           2 => 'female',
            1=>'boys',
            1=>'boy',
           2=> 'girl',
            2=>'girls'
            // Add other mappings if necessary
        ];

        return $genderMap[strtolower($gender)] ?? null;
    }


    private function mapMonth($monthStr)
    {
        $monthMap = [
            'january' => '01', 'february' => '02', 'march' => '03', 'april' => '04', 'may' => '05', 'june' => '06',
            'july' => '07', 'august' => '08', 'september' => '09', 'october' => '10', 'november' => '11', 'december' => '12'
        ];
        return isset($monthMap[strtolower($monthStr)]) ? $monthMap[strtolower($monthStr)] : null;
    }


    private function parseDate($dateStr)
    {
        dd($dateStr);
        // Use dateparser library in Python to convert date
        $response = Http::post('http://localhost:8080/parse_date', [
            'date_str' => $dateStr
        ]);

        return $response->body(); // Assuming the response is the parsed date in the desired format
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
    public function getVisitorCount($gender = null, $date = null)
    {

        $query = DB::table('counters');

        // Filter by gender if provided
        if ($gender) {

            $query->where('gender', $gender);
        }

        // Filter by date if provided
        if ($date) {
            $query->whereMonth('created_at', '=', $date);
        }

        // Count the number of visitors
        $count = $query->count();

        return $count;
    }

    public function decrement(Request $request)
    {
        $gender = $request->input('gender');
        $reason = 'incorrect entry';

        // Determine the gender value to decrement
        $genderValue = null;
        if (isset($gender) && $gender === 'male') {
            $genderValue = 1; // Male
        } elseif (isset($gender) && $gender === 'female') {
            $genderValue = 2; // Female
        } else {
            $genderValue = 3; // Others
        }

        // Check if any entries exist for today for the specified gender
        $todayCount = Counter::whereDate('created_at', today())
            ->where('gender', $genderValue)
            ->first();

        if ($todayCount) {
            // Soft delete the entry and store the reason
            $todayCount->deletion_reason = $reason; // Assign the reason for deletion
            $todayCount->save(); // Save the reason
            $todayCount->delete(); // Soft delete the entry

            return response()->json(['success' => true, 'message' => ucfirst($gender) . ' entry decremented.']);
        }

        return response()->json(['success' => false, 'message' => 'No entries found for the specified gender today.']);
    }

    public function incrementMultiple(Request $request){
            $user = Auth::user();

        $male = $request['maleCountInput'] ?? 0;
        $female = $request['femaleCountInput'] ?? 0;
        $other = $request['otherCountInput'] ?? 0; 
        if ($male > 0) {
            $counter = Counter::firstOrCreate(
                [
                    'user_id' => $user->id,
                    'gender' => 1,
                    'created_at' => Carbon::today()
                ],
                ['counts' => 0]
            );
            $counter->increment('counts', $male); 
        }

        if ($female > 0) {
            $counter = Counter::firstOrCreate(
                [
                    'user_id' => $user->id,
                    'gender' => 2,
                    'created_at' => Carbon::today()
                ],
                ['counts' => 0]
            );
            $counter->increment('counts', $female);
        }

        if ($other > 0) {
            $counter = Counter::firstOrCreate(
                [
                    'user_id' => $user->id,
                    'gender' => 3,
                    'created_at' => Carbon::today()
                ],
                ['counts' => 0]
            );
            $counter->increment('counts', $other); 
        }

        if ($request->ajax()) {
            \Log::info('AJAX request detected.');
            return response()->json(['success' => true]);
        }

        \Log::info('Non-AJAX request detected.');
        return redirect()->route('counter.report');

        }


}
