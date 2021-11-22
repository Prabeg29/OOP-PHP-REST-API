<?php


namespace App\Core;


use stdClass;

class Request
{
    public function __construct()
    {
        header("Access-Control-Allow-Origin: *");
        header("Content-Type: application/json; charset=UTF-8");
        //header("Access-Control-Allow-Methods: GET,POST,PUT,DELETE");
        header("Access-Control-Max-Age: 3600");
        header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");
    }

    /**
     * @return string
     */
    public function getMethod(): string
    {
        return $_SERVER['REQUEST_METHOD'];
    }

    /**
     * @return string
     */
    public function getUri(): string
    {
        return rtrim(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH));
    }

    public function getJson(): array
    {
        return (array) json_decode(trim(file_get_contents("php://input")));
    }
}
