<?php

namespace App\Http\Controllers;

use App\Models\NewsLetter;
use App\Http\Requests\StoreNewsLetterRequest;
use App\Http\Requests\UpdateNewsLetterRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;
use App\Models\User;

class NewsLetterController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {

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


        foreach (['Daily News' => 'Daily News', 'Guardian' => 'Guardian', 'Nipashe' => 'Nipashe','Uhuru' => 'Uhuru','Habari Leo' => 'Habari Leo','Citizen' => 'Citizen','East African' => 'East African','Mwananchi' => 'Mwananchi'] as $news => $label) {
            foreach (['yesterday', 'today', 'weekly', 'monthly', 'lastMonth', 'threeMonths', 'total'] as $period) {
                $user = auth()->user();

                $query = NewsLetter::where('user_id', $user->id)
                    ->where('name', $news);

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


        return view('newsletter.index', [
            'summary' => $summary,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */

    public function store(Request $request){

        $user = Auth::user();
    $newsletter = $request['newsletter'];
    $input = $request['newsletterInput'];
    $date = $request['dateCountInput'];

   $name = null;
    switch($newsletter){
        case 1:
            $name = 'Daily News';
            break;
            case 2:
                $name = 'Guardian';
                break;
            case 3:
                $name = 'Nipashe';
                break;
            case 4:
                $name = 'Uhuru';
                break;
            case 5:
                $name = 'Habari Leo';
                break;
            case 6:
                $name = 'Citizen';
                break;
            case 7:
                $name = 'East African';
                break;
            case 8:
                $name = 'Mwananchi';
                break;
    }

    if ($input > 0) {

        $news = NewsLetter::firstOrCreate(
            [
                'user_id' => $user->id,
                'name' => $name,
            ],
            ['counts' => 0]
        );
        $news->increment('counts', $input);
        $news->created_at = $date;
        $news->updated_at = $date;
        $news->save();
    }

    if ($request->ajax()) {
        \Log::info('AJAX request detected.');
        return response()->json(['success' => true]);
    }

    \Log::info('Non-AJAX request detected.');
    return redirect()->route('news_leter');

    }



    /**
     * Display the specified resource.
     */
    public function show(NewsLetter $newsLetter)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(NewsLetter $newsLetter)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateNewsLetterRequest $request, NewsLetter $newsLetter)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(NewsLetter $newsLetter)
    {
        //
    }
}
