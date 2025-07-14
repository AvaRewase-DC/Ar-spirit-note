<?php

namespace App\Repositories;

use Carbon\Carbon;

class GlobalApiRepository
{
    public function getCopticDate($day = null): string
    {
        $date = Carbon::parse($day ?? now());
        if ($date->hour >= 18) {
            $date->addDay();
        }
        $copticMonthsArabic = [
            1 => 'توت', 2 => 'بابه', 3 => 'هاتور', 4 => 'كيهك',
            5 => 'طوبه', 6 => 'أمشير', 7 => 'برمهات', 8 => 'برموده',
            9 => 'بشنس', 10 => 'بؤونه', 11 => 'أبيب', 12 => 'مسرى', 13 => 'النسئ',
        ];

        // Get Gregorian year of the date
        $gregYear = $date->year;

        // Determine the Coptic New Year in Gregorian
        $copticNewYear = Carbon::create($gregYear, 9, 11);
        if (Carbon::create($gregYear)->isLeapYear()) {
            $copticNewYear = Carbon::create($gregYear, 9, 12);
        }

        // If date is before Coptic New Year, use previous year’s Coptic New Year
        if ($date->lt($copticNewYear)) {
            $copticNewYear = $copticNewYear->subYear();
        }

        $daysSinceNewYear = $date->diffInDays($copticNewYear);
        $copticYear = $copticNewYear->year - 283;

        $copticMonth = (int) floor($daysSinceNewYear / 30) + 1;
        $copticDay = ($daysSinceNewYear % 30) + 1;

        $monthName = $copticMonthsArabic[$copticMonth] ?? 'غير معروف';

        return "{$copticDay} {$monthName} {$copticYear}";
    }
}
