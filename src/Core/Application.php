<?php


namespace App\Core;

use App\Core\Request;
use App\Core\Response;
use App\Core\Router;
use App\Core\Database;
use Exception;


class Application
{
    protected Router $router;

    public function __construct()
    {
        $this->router = new Router();
    }

    public function run()
    {
        Router::loadRoutes('../src/routes.php');
        try{
            echo $this->router->resolve();
        }
        catch(Exception $ex){
            echo $ex->getMessage();
        }
    }
}