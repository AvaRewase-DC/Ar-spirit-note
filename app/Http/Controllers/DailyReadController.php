<?php

namespace App\Http\Controllers;

use App\Models\DailyRead;
use App\Repositories\DailyReadRepository;
use Carbon\Carbon;
use Illuminate\Http\Request;

class DailyReadController extends Controller
{
    public function __construct(protected DailyReadRepository $dailyReadRepository) {}

    public function index(Request $request)
    {
        $year = $request->year ?? now()->year;
        $month = $request->month ?? now()->month;
        $startOfMonth = Carbon::create($year, $month, 1)->startOfDay();
        $endOfMonth = Carbon::create($year, $month, 1)->endOfMonth()->endOfDay();
        $reads = DailyRead::whereDate('day', '>=', $startOfMonth)
            ->whereDate('day', '<=', $endOfMonth)
            ->get();

        if ($request->ajax()) {
            return view('daily-read.partial-table', compact('reads', 'year', 'month'))->render();
        }

        return view('daily-read.index', compact('reads'));
    }

    public function show(Request $request)
    {
        $read = DailyRead::whereDate('day', '=', $request->date ?? today())->first();

        return view('daily-read.show', compact('read'));
    }

    public function create()
    {
        return view('daily-read.create');
    }

    public function store(Request $request)
    {
        $this->dailyReadRepository->store($request);

        return redirect()->back()->with('message', 'Daily read created successfully');
    }
}
