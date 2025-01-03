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

        return view('report.index')->with('data', $data);
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
