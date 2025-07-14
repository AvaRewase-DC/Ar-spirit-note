@php
    use Carbon\Carbon;

    $startOfMonth = Carbon::create($year, $month, 1);
    $daysInMonth = $startOfMonth->daysInMonth;
    $startDayOfWeek = $startOfMonth->dayOfWeek; // 0 = Sunday, ..., 6 = Saturday
    $dayCounter = 1;
    $today = now()->toDateString();

    $daysArabic = ['الأحد', 'الاثنين', 'الثلاثاء', 'الأربعاء', 'الخميس', 'الجمعة', 'السبت'];
@endphp

<style>
    td.today {
        background-color: #ffe5e5;
    }

    .date-number {
        font-weight: bold;
        font-size: 0.8em;
        margin-bottom: 5px;
    }

    .read-part {
        font-size: 0.9em;
        color: #333;
    }

    .saints {
        font-size: 0.8em;
        color: darkred;
    }

    a.day-link {
        text-decoration: none;
        color: inherit;
        display: block;
        padding: 5px;
    }
</style>

<table>
    <thead>
        <tr>
            @foreach ($daysArabic as $dayName)
                <th>{{ $dayName }}</th>
            @endforeach
        </tr>
    </thead>
    <tbody>
        @while ($dayCounter <= $daysInMonth)
            <tr>
                @for ($i = 0; $i < 7; $i++)
                    @if ($dayCounter === 1 && $i < $startDayOfWeek)
                        <td></td>
                    @elseif ($dayCounter > $daysInMonth)
                        <td></td>
                    @else
                        @php
                            $currentDate = Carbon::create($year, $month, $dayCounter)->toDateString();
                            $read = $reads->firstWhere('day', $currentDate);
                            $isToday = $currentDate === $today;
                        @endphp

                        <td class="{{ $isToday ? 'today' : '' }}">
                            <a href="{{ route('daily-read.show', ['date' => $currentDate]) }}" class="day-link">
                                <div class="date-number">
                                    {{ $dayCounter }}
                                    @if ($read)
                                        <br>{{ $read->getCopticDate() }}
                                    @endif
                                </div>

                                @if ($read)
                                    <hr>
                                    <div class="read-part">
                                        {{ Str::limit($read->read_parts, 40) }}
                                    </div>

                                    @if ($read->relationLoaded('saintFests') && $read->saintFests && $read->saintFests->isNotEmpty())
                                        <hr>
                                        <div class="saints">
                                            @foreach ($read->saintFests as $fest)
                                                • {{ $fest->title }}<br>
                                            @endforeach
                                        </div>
                                    @endif
                                @endif
                            </a>
                        </td>

                        @php $dayCounter++; @endphp
                    @endif
                @endfor
            </tr>
        @endwhile
    </tbody>
</table>
