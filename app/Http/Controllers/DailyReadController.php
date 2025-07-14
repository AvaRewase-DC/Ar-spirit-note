<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateDailyReadRequest;
use App\Http\Requests\ShowDailyReadRequest;
use App\Http\Requests\UpdateDailyReadRequest;
use App\Repositories\DailyReadRepository;
use Illuminate\Http\Request;

class DailyReadController extends Controller
{
    public function __construct(protected DailyReadRepository $dailyReadRepository) {}

    public function index(Request $request)
    {
        $year = $request->input('year', now()->year);
        $month = $request->input('month', now()->month);

        validator([
            'year' => $year,
            'month' => $month,
        ], [
            'year' => 'required|integer|in:2025,2026',
            'month' => 'required|integer|between:1,12',
        ])->validate();

        $reads = $this->dailyReadRepository->index($year, $month);

        if ($request->ajax()) {
            return view('daily-read.partial-table', compact('reads', 'year', 'month'))->render();
        }

        return view('daily-read.index', compact('reads', 'year', 'month'));
    }

    public function show(ShowDailyReadRequest $request)
    {
        $read = $this->dailyReadRepository->show($request->date);
        $read->load('saintFests', 'videos');

        return view('daily-read.show', compact('read'));
    }

    public function create()
    {
        return view('daily-read.create');
    }

    public function store(CreateDailyReadRequest $request)
    {
        $this->dailyReadRepository->store($request->validated());

        return redirect()->back()->with('success', 'Daily read created successfully');
    }

    public function edit($id)
    {
        $read = $this->dailyReadRepository->edit($id);

        return view('daily-read.edit', compact('read'));
    }

    public function update(UpdateDailyReadRequest $request, $id)
    {
        $read = $this->dailyReadRepository->update($request, $id);

        return redirect()
            ->route('daily-read.show', $read->day)
            ->with('success', 'تم تحديث القراءة اليومية بنجاح.');
    }
}
