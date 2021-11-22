<?php


namespace App\Core;


use Exception;

class Router
{
    protected static array $routes = [];

    protected Request $request;
    protected Response $response;

    public function __construct()
    {
        $this->request = new Request();
        $this->response = new Response();
    }

    public static function get(string $uri, array $callback)
    {
        self::$routes['GET'][$uri] = $callback;
    }

    public static function post(string $uri, array $callback)
    {
        self::$routes['POST'][$uri] = $callback;
    }

    public static function put(string $uri, array $callback)
    {
        self::$routes['PUT'][$uri] = $callback;
    }

    public static function delete(string $uri, array $callback)
    {
        self::$routes['DELETE'][$uri] = $callback;
    }

    public static function loadRoutes(string $routeFile)
    {
        return require_once $routeFile;
    }

    public function resolve()
    {
        $method = $this->request->getMethod();
        $uri = $this->request->getUri();

        $id = substr(ltrim($uri, '/api/v1'), strpos(ltrim($uri, '/api/v1'), '/')+1) ?? '';

        if(is_string($id) && preg_match('/^[0-9]*$/', $id)){
            $oldUri = substr($uri, 0, strpos(ltrim($uri, '/api/v1'), '/')+10)  . '{$id}';
            self::$routes[$method][$uri] = self::$routes[$method][$oldUri];
        }

        $callback = self::$routes[$method][$uri] ?? false;

        if(!$callback){
            $this->response->setStatusCode(500);
            throw new Exception('Route not defined');
        }

        $callback[0] = new $callback[0];

        return call_user_func($callback, $this->request, $this->response, $id);
    }
}
