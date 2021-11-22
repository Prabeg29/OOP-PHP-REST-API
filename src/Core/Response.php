<?php


namespace App\Core;


class Response
{
    public function setStatusCode(int $statusCode)
    {
        http_response_code($statusCode);
    }

    private function toJson(array $data)
    {
        return json_encode($data);
    }

    public function json(array $data, int $statusCode)
    {
        $this->setStatusCode($statusCode);
        //header('Content-Type: application/json');
        return $this->toJson($data);
    }
}
