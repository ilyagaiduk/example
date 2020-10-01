<?php

namespace App;
use Illuminate\Support\Facades\DB;
use App\Coordinates;
use Carbon\Carbon;

class Sun extends Coordinates
{
    public function sunriseNow($lat, $lng, $iana) {
        date_default_timezone_set($iana);
        $lastDay = Carbon::now()->subDay()->format('Y-m-d H:i:s');
        $now = Carbon::now()->format('Y-m-d H:i:s');
        $nextDay = Carbon::now()->addDay()->format('Y-m-d H:i:s');
        $massDate = [$lastDay, $now, $nextDay];
        $day = [];
        $a = 0;
        foreach($massDate as $key) {
            $sun_info = date_sun_info(strtotime($key), $lat, $lng);
            $a++;
            $sunrise = date("H:i:s", $sun_info['sunrise']);
            $transit = date("H:i:s", $sun_info['transit']);
            $sunset = date("H:i:s", $sun_info['sunset']);
            $day[$a] = $sunrise;
            $a++;
            $day[$a] = $transit;
            $a++;
            $day[$a] = $sunset;
        }
        $dateSunriseToday = Carbon::createFromTimeString($day[4]);
        $dateSunriseYestoday = Carbon::createFromTimeString($day[1]);
        $dateSunsetToday = Carbon::createFromTimeString($day[6]);
        $dateSunsetYestoday = Carbon::createFromTimeString($day[3]);
        $totalDurationSunrise = $dateSunriseYestoday->diffInMinutes($dateSunriseToday, false);
        $totalDurationSunset = $dateSunsetYestoday->diffInMinutes($dateSunsetToday, false);
        if($totalDurationSunrise < 0) {
            $color = 'red';
        }
        elseif($totalDurationSunrise > 0) {
            $totalDurationSunrise = "+".$totalDurationSunrise;
            $color = 'green';
        }
        elseif($totalDurationSunrise === 0){
            $color = 'black';
        }
        if($totalDurationSunset < 0) {
            $color = 'red';
        }
        elseif($totalDurationSunset > 0) {
            $color = 'green';
            $totalDurationSunset = "+".$totalDurationSunset;
        }
        elseif($totalDurationSunset === 0){
            $color = 'black';
        }
        $printSunrise = " <span style='color:$color'><small>".$totalDurationSunrise."</span> мин.</small>";
        $printSunset = " <span style='color:$color'><small>".$totalDurationSunset."</span> мин.</small>";
        if($totalDurationSunset === 0) {
            $printSunset = '';
        }
        if($totalDurationSunrise === 0) {
            $printSunrise = '';
        }

        $day[4] = $day[4].$printSunrise;
        $day[6] = $day[6].$printSunset;

        return $day;


    }
    public function getSunContUrl($cont) {
        Coordinates::getContURL($cont);
    }
    public function getSunCountry($cont) {
        Coordinates::getCountry($cont);
    }
    public function getSunCountryUrl($country){
        Coordinates::getCountryURL($country);
    }
    public function getSunCity($country){
        Coordinates::getCity($country);
    }
    public function getSunCountryh1($country){
        Coordinates::getCountryh1($country);
    }
public function getSunriseNowMonth($lat, $lng, $iana, $month, $year, $monthLenght) {
    date_default_timezone_set($iana);

        $monthRu = ['01' => 'Января', '02' => 'Февраля', '03' => 'Марта', '04' =>'Апреля', '05' =>'Мая', '06' =>'Июня',
            '07' =>'Июля', '08' => 'Августа', '09' =>'Сентября', '10' =>'Октября', '11' =>'Ноября', '12' =>'Декабря'];
        $SunOnMonth = [];
        for($i = 1; $i<= $monthLenght; $i++) {
            $sun_info = date_sun_info(strtotime(Carbon::create($year, $month, $i, 0, 0, 0, $iana)
                ->format('Y-m-d')), $lat, $lng);

            $sunrise = Carbon::createFromTimeString(date("H:i:s", $sun_info['sunrise']));
            $sunset = Carbon::createFromTimeString(date("H:i:s", $sun_info['sunset']));
            $totalDurationDay = $sunrise->diffInSeconds($sunset, false);
            $seconds = $totalDurationDay; // Количество исходных секунд
            $minutes = floor($seconds / 60); // Считаем минуты
            $hours = floor($minutes / 60); // Считаем количество полных часов
            $minutes = $minutes - ($hours * 60);  // Считаем количество оставшихся минут
            $dayLenght = $hours.' ч. '.$minutes. " мин.";
            $sun_info['DayLenght'] = $dayLenght;
            $sun_info['DayLenghthour'] = $hours;
            $sun_info['DayLenghtmin'] = $minutes;
            $sun_info['Month'] = $monthRu[$month];
            $SunOnMonth[$i] = $sun_info;
        }
       return $SunOnMonth;
}
    public function getSunriseNowYear($lat, $lng, $iana, $year) {
        date_default_timezone_set($iana);

        $SunInYear = [];
        for($x = 1; $x<=12; $x++) {

                $sun_info = date_sun_info(strtotime(Carbon::create($year, $x, 1, 0, 0, 0, $iana)
                    ->format('Y-m-d')), $lat, $lng);

                $sunrise = Carbon::createFromTimeString(date("H:i:s", $sun_info['sunrise']));
                $sunset = Carbon::createFromTimeString(date("H:i:s", $sun_info['sunset']));
                $totalDurationDay = $sunrise->diffInSeconds($sunset, false);
                $seconds = $totalDurationDay; // Количество исходных секунд
                $minutes = floor($seconds / 60); // Считаем минуты
                $hours = floor($minutes / 60); // Считаем количество полных часов
                $minutes = $minutes - ($hours * 60);  // Считаем количество оставшихся минут
                $dayLenght = $hours . ' ч. ' . $minutes . " мин.";
                $sun_info['DayLenght'] = $dayLenght;
                $sun_info['DayLenghthour'] = $hours;
                $sun_info['DayLenghtmin'] = $minutes;
                $sun_info['Month'] = $x;
                $SunInYear[$x] = $sun_info;
        }
        return $SunInYear;
    }

}
