@php
    use Carbon\Carbon;

    $startOfMonth = Carbon::create($year, $month, 1);
    $daysInMonth = $startOfMonth->daysInMonth;
    $startDayOfWeek = $startOfMonth->dayOfWeek; // 0 = Sunday, 1 = Monday, ..., 6 = Saturday
    $dayCounter = 1;

    $daysArabic = ['الأحد', 'الاثنين', 'الثلاثاء', 'الأربعاء', 'الخميس', 'الجمعة', 'السبت'];
@endphp

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
                        @endphp
                        <td>
                            <a href="{{ route('daily-read.show', ['date' => $currentDate]) }}"
                                style="text-decoration: none; color: inherit; display: block;">
                                <div class="date-number">{{ $dayCounter }} @if (isset($reads[$dayCounter - 1]) && Carbon::parse($reads[$dayCounter - 1]->day)->day == $dayCounter)
                                        <br> {{ $reads[$dayCounter - 1]->getCopticDate() }}
                                    @endif
                                </div>
                                <hr>
                                @if (isset($reads[$dayCounter - 1]) && Carbon::parse($reads[$dayCounter - 1]->day)->day == $dayCounter)
                                    {{ $reads[$dayCounter - 1]?->read_parts }}
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
