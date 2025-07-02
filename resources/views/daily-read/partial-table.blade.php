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
                        {{-- Before the 1st of the month, add empty cells --}}
                        <td></td>
                    @elseif ($dayCounter > $daysInMonth)
                        {{-- After the last day of the month, add empty cells --}}
                        <td></td>
                    @else
                        {{-- Fill in valid days --}}
                        <td>
                            <div class="date-number">{{ $dayCounter }}</div>
                            <hr>
                            @if (isset($reads[$dayCounter - 1]))
                                {{ $reads[$dayCounter - 1]?->read_parts }}
                            @endif
                        </td>
                        @php $dayCounter++; @endphp
                    @endif
                @endfor
            </tr>
        @endwhile
    </tbody>
</table>
