<?php

namespace App\Repositories;

use Carbon\Carbon;
use chillerlan\QRCode\QRCode;
use chillerlan\QRCode\QROptions;

class GlobalApiRepository
{
    public function getCopticDate($day = null)
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

        return compact('copticDay', 'monthName', 'copticYear');
    }

    public function generateQrCode($data, $size = 400, $format = 'png')
    {
        $options = new QROptions([
            'version' => -1, // Auto-detect version based on data length
            'outputType' => $format === 'svg' ? QRCode::OUTPUT_MARKUP_SVG : QRCode::OUTPUT_IMAGE_PNG,
            'eccLevel' => QRCode::ECC_L,  // Low error correction for larger data capacity
            'scale' => (int) ($size / 50), // Scale to approximate the size
            'imageBase64' => false,
        ]);

        $qrcode = new QRCode($options);

        return $qrcode->render($data);
    }
}
