<?php

namespace App\Http\Controllers;

use App\Models\Counter;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class reportController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $data = DB::table('counters')
            ->select(
                DB::raw('DATE(counters.created_at) as date'),
                'users.id as user_id',
                DB::raw('SUM(CASE WHEN counters.gender = 1 THEN 1 ELSE 0 END) as male_count'),
                DB::raw('SUM(CASE WHEN counters.gender = 2 THEN 1 ELSE 0 END) as female_count'),
                DB::raw('SUM(CASE WHEN counters.gender = 3 THEN 1 ELSE 0 END) as other_count'),
                DB::raw('COUNT(*) as total_count'),
                'users.*',
            )
            ->join('users', 'counters.user_id', '=', 'users.id')
            ->whereNull('counters.deleted_at')
            ->groupBy(DB::raw('DATE(counters.created_at)'), 'users.id')
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
