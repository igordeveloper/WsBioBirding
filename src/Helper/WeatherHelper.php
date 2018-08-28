<?php

namespace App\Helper;

class WeatherHelper
{

    private $secretKeyDarkSky = "8987f4fc7f91d7ecd89cf9df78444281";
    private $url = "https://api.darksky.net";

    public function check(/*float $latitude, float $longitude, int $timestamp*/): bool
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_URL, 'https://api.darksky.net/forecast/8987f4fc7f91d7ecd89cf9df78444281/-23.177249,%20-46.768430,1535315835?exclude=hourly,%20flags,alerts&lang=pt&units=si');
        $result=curl_exec($ch);
        curl_close($ch);

        var_dump(json_decode($result, true));
        return true;

    }
}