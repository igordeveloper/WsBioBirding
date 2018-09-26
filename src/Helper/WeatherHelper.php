<?php

namespace App\Helper;

class WeatherHelper
{

    private $secretKeyDarkSky;

    public function __construct(){
        $this->secretKeyDarkSky = $_SERVER['KEY_DARK_SKY'];
    }

    public function check(float $latitude, float $longitude, float $timestamp): array
    {

        $ch = \curl_init();
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_URL, "https://api.darksky.net/forecast/".$this->secretKeyDarkSky."/".$latitude.",".$longitude.",".$timestamp."?exclude=hourly,%20flags,alerts&lang=pt&units=ca");
        $result=curl_exec($ch);
        curl_close($ch);

        $response = json_decode($result, true);

        $array = [];
        $array['weather'] = $response['currently']['summary'];
        $array['temperature'] = $response['currently']['temperature'];
        $array['windSpeed'] = $response['currently']['windSpeed'];
        $array['humidity'] = $response['currently']['humidity'] * 100;

        return $array;

    }
}