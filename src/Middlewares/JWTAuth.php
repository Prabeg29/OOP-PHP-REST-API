<?php

namespace App\Middlewares;

use Exception;
use Firebase\JWT\JWT;

class JWTAuth
{
    public static function auth()
    {
        $secret_key= "rest-api-oop-php";

        $authHeader = $_SERVER['HTTP_AUTHORIZATION'];

        $jwt = explode(" ", $authHeader)[1];

        if(!$jwt)
        {
            http_response_code(401);
            exit(json_encode([
                "message" => "Unauthorized"]));

        }

        try {
            $decoded = JWT::decode($jwt, $secret_key, array('HS256'));
            return $decoded->data->id;
        } catch (Exception $ex) {
            http_response_code(401);
            exit(json_encode([
                "message" => "Unauthorized",
                "error" => $ex->getMessage()]));
        }
    }
}
