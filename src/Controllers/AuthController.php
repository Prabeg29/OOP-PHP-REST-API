<?php


namespace App\Controllers;


use App\Core\Controller;
use App\Core\Request;
use App\Core\Response;
use App\Models\User;
use App\Traits\JWTAuth;
use Exception;

class AuthController extends Controller
{
    use JWTAuth;

    protected User $user;

    public function __construct()
    {
        $this->user = new User();
    }

    /**
     * @param Request $request
     * @param Response $response
     * @return false|string
     */
    public function register(Request $request, Response $response)
    {
        header("Access-Control-Allow-Methods: POST");

        $this->json['data'] = $request->getJson();

        $this->json['data']['password'] = password_hash(
            $this->json['data']['password'],
            PASSWORD_DEFAULT);

        try{
            $user = $this->user->createUser([
                'username' => $this->json['data']['username'],
                'email' => $this->json['data']['email'],
                'password' => $this->json['data']['password']
            ]);
            return $response->json(array("user"=>$user), 201);

        }catch(Exception $ex){
            return $response->json(array("message" => $ex->getMessage()), 500);
        }
    }

    /**
     * @param Request $request
     * @param Response $response
     * @return false|string
     * @throws Exception
     */
    public function login(Request $request, Response $response)
    {
        header("Access-Control-Allow-Methods: POST");

        $this->json['data'] = $request->getJson();

        try{
            $user = $this->user->getUserWhere(['username' => $this->json['data']['username']]);

            if($user && password_verify($this->json['data']['password'], $user['password'])){
                return $response->json(array(
                    "status" => "Successful Login",
                    "token_type" => "bearer",
                    "token" => $this->generateJWT((array) $user)
                ), 200);
            }

            $this->json['error'] = "Invalid Credentials. Please try again";

            return $response->json(array("message" => $this->json['error']), 403);
        }
        catch(Exception $ex)
        {
            return $response->json(array("message" => $ex->getMessage()), 500);
        }
    }
}
