<?php

namespace App\Http\Controllers;

use App\Models\DailyRead;
use Carbon\Carbon;
use Illuminate\Http\Request;

class DailyReadController extends Controller
{
    public function index()
    {
        $startOfMonth = Carbon::create(now()->year, now()->month, 1)->startOfDay();
        $endOfMonth = Carbon::create(now()->year, now()->month, 1)->endOfMonth()->endOfDay();
        $reads = DailyRead::whereDate('day', '>=', $startOfMonth)
                        ->whereDate('day', '<=', $endOfMonth)
                        ->get();
        return view('daily-read.index', compact('reads'));
    }
}
