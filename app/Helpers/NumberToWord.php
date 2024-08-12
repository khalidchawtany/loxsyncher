<?php

namespace App\Helpers;

class NumberToWord
{
    public static function convert($number)
    {
        $number = str_replace(['.', ','], ['', ''], $number);
        $hyphen = ' و ';
        $conjunction = ' و ';
        $separator = ' و ';
        $negative = 'كه‌م ';
        $decimal = ' پۆینت ';
        $dictionary = [
            0 => 'سفر',
            1 => 'یەک',
            2 => 'دوو',
            3 => 'سێ',
            4 => 'چوار',
            5 => 'پێنج',
            6 => 'شەش',
            7 => 'حەوت',
            8 => 'هەشت',
            9 => 'نۆ',
            10 => 'دە',
            11 => 'یازدە',
            12 => 'دوازدە',
            13 => 'سیازدە',
            14 => 'چواردە',
            15 => 'پازدە',
            16 => 'شازدە',
            17 => 'حەڤدە',
            18 => 'هەژدە',
            19 => 'نۆزدە',
            20 => 'بیست',
            30 => 'سی',
            40 => 'چل',
            50 => 'پەنجا',
            60 => 'شەست',
            70 => 'حەفتا',
            80 => 'هەشتا',
            90 => 'نەوەد',
            100 => 'سەد',
            1000 => 'هەزار',
            1000000 => 'ملیۆن',
            1000000000 => 'بلیۆن',
            1000000000000 => 'ترلیۆن',
        ];

        if (!is_numeric($number)) {
            return false;
        }

        if (($number >= 0 && (int) $number < 0) || (int) $number < 0 - PHP_INT_MAX) {
            // overflow
            trigger_error(
                'convert only accepts numbers between -' . PHP_INT_MAX . ' and ' . PHP_INT_MAX,
                E_USER_WARNING
            );

            return false;
        }

        if ($number < 0) {
            return $negative . self::convert(abs($number));
        }

        $string = $fraction = null;

        if (strpos($number, '.') !== false) {
            [$number, $fraction] = explode('.', $number);
        }

        switch (true) {
            case $number < 21:
                $string = $dictionary[$number];
                break;
            case $number < 100:
                $tens = ((int) ($number / 10)) * 10;
                $units = $number % 10;
                $string = $dictionary[$tens];
                if ($units) {
                    $string .= $hyphen . $dictionary[$units];
                }
                break;
            case $number < 1000:
                $hundreds = $number / 100;
                $remainder = $number % 100;
                $string = $dictionary[100];
                if (floor($hundreds) > 1) {
                    $string = $dictionary[$hundreds] . ' ' . $string;
                }
                if ($remainder) {
                    $string .= $conjunction . self::convert($remainder);
                }
                break;
            default:
                $baseUnit = pow(1000, floor(log($number, 1000)));
                $numBaseUnits = (int) ($number / $baseUnit);
                $remainder = $number % $baseUnit;
                $string = $dictionary[$baseUnit];
                if (!($numBaseUnits == 1 && $baseUnit == 1000)) {
                    $string = self::convert($numBaseUnits) . ' ' . $string;
                }

                if ($remainder) {
                    $string .= $remainder < 100 ? $conjunction : $separator;
                    $string .= self::convert($remainder);
                }
                break;
        }

        if (null !== $fraction && is_numeric($fraction)) {
            if (intval($fraction) != 0) {
                $string .= $decimal;
                $string .= self::convert($fraction);
            }
            // $words = array();
            // foreach (str_split((string) $fraction) as $number) {
            //     $words[] = $dictionary[$number];
            // }
            // $string .= implode(' ', $words);
        }

        return $string;
    }
}
