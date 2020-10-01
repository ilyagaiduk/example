<?php

namespace App\Http\Controllers;

use App\Coordinates;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Sun;
use Illuminate\Support\Facades\DB;

class SunController extends Controller
{
    //Долгота Континенты
    public function sunCont($cont) {
        $cont = htmlspecialchars($cont);
        $models = new Sun();
        $data = $models->getCountry($cont);
        $continent = $models->getContUrl($cont);
        if($continent->aliascont == $cont) {
            return view('sun_cont_index', ['data' => $data, 'continenta' => $continent]);

        }
    }
    //Долгота Страны
    public function sunCountry($country) {
        $models = new Sun();
        if($models->getCountryURL($country) == $country) {
            $data = $models->getCity($country);

            $strani = $models->getCountryh1($country);
            return view('sun_country_index', ['data' => $data, 'strani' => $strani]);

        }
    }
    //Долгота Города
    public function sunCity($id, Request $request) {
        $data = DB::table('cityallnew')
            ->where('id', '=', htmlspecialchars($id))
            ->first();
        if ($request->isMethod('post')) {
            $month = $request->month;
            $year = $request->year;
            $month = Carbon::create($year, $month, 1, 0, 0, 0, $data->iana)->format('m');
            $monthLenght = Carbon::create($year, $month, 1, 0, 0, 0, $data->iana)->format('t');
            $year = Carbon::create($year, $month, 1, 0, 0, 0, $data->iana)->format('Y');
        }
        else{
            date_default_timezone_set($data->iana);
            $month = Carbon::now()->format('m');
            $monthLenght = Carbon::now()->format('t'); //количество дней в текущем месяце
            $year = Carbon::now()->format('Y');
        }
        $model = new Sun();
        $SunYear = $model->getSunriseNowYear($data->lat, $data->lng, $data->iana, $request->year);
        $SunMonth = $model->getSunriseNowMonth($data->lat, $data->lng, $data->iana, $month, $year, $monthLenght);
        $SunDay = $model->sunriseNow($data->lat, $data->lng, $data->iana);
        $monthRu = ['01' => 'Январь', '02' => 'Февраль', '03' => 'Март', '04' =>'Апрель', '05' =>'Май', '06' =>'Июнь',
            '07' =>'Июль', '08' => 'Август', '09' =>'Сентябрь', '10' =>'Октябрь', '11' =>'Ноябрь', '12' =>'Декабрь'];
        $models = new Coordinates();
        $dataAboutCity  = $models->getCityURL($id);
        $dataNearCity = $models->GetNearCity($url = 'sunrise');
        $dataBigCity = $models->GetBigCity();
        if($data->id == $id) {
            return view('sun_city_index', ['data' => $data, 'SunMonth' => $SunMonth,
                'Day' => $SunDay, 'monthRu' => $monthRu, 'month' => $month, 'year' => $year, 'monthLenght'=> $monthLenght,
                'SunYear'=>$SunYear, 'dataNearCity'=>$dataNearCity, 'dataBigCity' => $dataBigCity]);
        }
    }
}
