<?php

namespace App\Utils;




class Autenticar
{

    public function token(string $token): bool
    {
    	if($token == "78b6854e22ab09d4ae3dac29b92052963103b33e"){
			return true;
    	}else if(is_null($token)){
    		throw new Exception('Divisão por zero.');
    	}
    }
}