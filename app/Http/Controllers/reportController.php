<?php

namespace App\Http\Controllers;

use App\Models\Counter;
use App\Models\Proposal;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class reportController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function search(Request $request)
    {
        $query = Proposal::query();

        if ($request->filled('search')) {
            $search = strtolower($request->search);
            $query->whereRaw('LOWER(title) LIKE ?', ["%{$search}%"]);
        }

        if ($request->filled('from')) {
            $query->whereDate('timeline', '>=', $request->from);
        }

        if ($request->filled('to')) {
            $query->whereDate('timeline', '<=', $request->to);
        }


        $proposals = $query->latest()->paginate(10)->withQueryString();


        return view('proposals.partials.table', compact('proposals'));
    }

    public function createProposal()
    {
        return view('proposals.create');
    }

    public function storeProposal(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'timeline' => 'required|string|max:255',
            'details' => 'required|string',
            'attachment' => 'required|mimes:pdf|max:5048', // only PDF, max 2MB
        ]);

        $filePath = $request->file('attachment')->store('proposals', 'public');
       Proposal::create([
            'title' => $request->title,
            'timeline' => $request->timeline,
            'details' => $request->details,
            'attachment' => $filePath,
        ]);


        return redirect()->route('proposal')->with('success', 'Proposal submitted successfully!');
    }
    public function indexProposal(Request $request)
    {
        $query = Proposal::query();

        if ($request->filled('search')) {
            $query->where('title', 'like', '%' . $request->search . '%');
        }

        // Filter by date range
        if ($request->filled('from') && $request->filled('to')) {
            $query->whereBetween('created_at', [$request->from, $request->to]);
        }

        $proposals = $query->latest()->paginate(10)->withQueryString();

        return view('proposal', compact('proposals'));
    }

    public function index()
    {
        $data = DB::table('counters')
            ->select(
                DB::raw('DATE(counters.created_at) as date'),
                'users.id as user_id',
                'users.name', // Explicitly select 'name' from 'users'
                DB::raw('SUM(CASE WHEN counters.gender = 1 THEN counters.counts ELSE 0 END) as male_count'),
                DB::raw('SUM(CASE WHEN counters.gender = 2 THEN counters.counts ELSE 0 END) as female_count'),
                DB::raw('SUM(CASE WHEN counters.gender = 3 THEN counters.counts ELSE 0 END) as other_count'),
                DB::raw('SUM(counters.counts) as total_count') // Sum all counts for each user per date
            )
            ->join('users', 'counters.user_id', '=', 'users.id')
            ->whereNull('counters.deleted_at')
            ->groupBy(DB::raw('DATE(counters.created_at)'), 'users.id', 'users.name') // Group by date, user_id, and name
            ->orderByDesc(DB::raw('DATE(counters.created_at)'))
            ->get();



        $news = DB::table('news_letters')
            ->select(
                DB::raw('DATE(news_letters.created_at) as date'),
                'users.name as user_name',
                DB::raw("SUM(CASE WHEN news_letters.name = 'Guardian' THEN news_letters.counts ELSE 0 END) as guardian_count"),
                DB::raw("SUM(CASE WHEN news_letters.name = 'Nipashe' THEN news_letters.counts ELSE 0 END) as nipashe_count"),
                DB::raw("SUM(CASE WHEN news_letters.name = 'Habari Leo' THEN news_letters.counts ELSE 0 END) as habari_leo_count"),
                DB::raw("SUM(CASE WHEN news_letters.name = 'Uhuru' THEN news_letters.counts ELSE 0 END) as uhuru_count"),
                DB::raw("SUM(CASE WHEN news_letters.name = 'East African' THEN news_letters.counts ELSE 0 END) as east_african_count"),
                DB::raw("SUM(CASE WHEN news_letters.name = 'Mwananchi' THEN news_letters.counts ELSE 0 END) as mwananchi_count"),
                DB::raw("SUM(CASE WHEN news_letters.name = 'Citizen' THEN news_letters.counts ELSE 0 END) as citizen_count"),
                DB::raw("SUM(CASE WHEN news_letters.name = 'Daily News' THEN news_letters.counts ELSE 0 END) as daily_news_count"),
                DB::raw("SUM(CASE WHEN news_letters.name IN ('Guardian', 'Nipashe', 'Habari Leo', 'Uhuru', 'East African', 'Mwananchi', 'Citizen', 'Daily News') THEN news_letters.counts ELSE 0 END) as total_count")
            )
            ->join('users', 'news_letters.user_id', '=', 'users.id')
            ->groupBy(DB::raw('DATE(news_letters.created_at)'), 'users.name')
            ->orderByDesc(DB::raw('DATE(news_letters.created_at)'))
            ->get();



        return view('report.index')->with('data', $data)->with('news',$news);
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
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }

    public function getData(){
        dd(100);
    }
}
