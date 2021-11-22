<?php

namespace App\Traits;

use Firebase\JWT\JWT;

trait JWTAuth
{
    public function generateJWT(array $payload): string
    {
        $iat = time();
        $exp = $iat + (60 * 60);
        $iss = "localhost";
        $key = $_ENV['APP_KEY'];

        $token = array(
            "iat" => $iat,
            "exp" => $exp,
            "iss" => $iss,
            "data" => $payload
        );

        return JWT::encode($token, $key, 'HS256');
    }
}
