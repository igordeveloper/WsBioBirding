<?php

namespace App\Helper;

class LocationHelper
{

    private $key;

    public function __construct(){
        $this->key = $_SERVER['KEY_GOOGLE'];
    }

    public function check(float $latitude, float $longitude): array
    {

        $ch = \curl_init();
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_URL, "https://maps.googleapis.com/maps/api/geocode/json?latlng=".$latitude.",".$longitude."&sensor=true&language=pt-BR&key=".$this->key."");
        $result=curl_exec($ch);
        curl_close($ch);

        $response = json_decode($result, true);
        $location = $response["results"][0]; 

        $address = [];
        $address['street'] = null;
        $address['streetNumber'] = null;
        $address['neighborhood'] = null;
        $address['city'] = null;
        $address['state'] = null;
        $address['country'] = null;

        foreach ($location['address_components'] as $value) {

            if(in_array('route', $value['types'])){
                $address['street'] = $value['long_name'];
            }

            if(in_array('street_number', $value['types'])){
                $address['streetNumber'] = $value['long_name'];
            }

            if(in_array('sublocality_level_1', $value['types'])){
                $address['neighborhood'] = $value['long_name'];
            }


            if(in_array('administrative_area_level_2', $value['types'])){
                $address['city'] = $value['long_name'];
            }

            if(in_array('administrative_area_level_1', $value['types'])){
                $address['state'] = $value['long_name'];
            }

            if(in_array('country', $value['types'])){
                $address['country'] = $value['long_name'];
            }

        }

        return $address;

    }
}