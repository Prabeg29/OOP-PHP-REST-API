<?php


namespace App\Controllers;


use App\Core\Controller;
use App\Core\Request;
use App\Core\Response;
use App\Middlewares\JWTAuth;
use App\Models\Post;
use Exception;

class UserPostController extends Controller
{
    /**
     * @var Post
     */
    private Post $post;
    private $userId;

    public function __construct()
    {
        $this->userId = JWTAuth::auth();
        $this->post = new Post();
    }

    /**
     * @param Request $request
     * @param Response $response
     * @return false|string
     */
    public function index(Request $request, Response $response)
    {
        header("Access-Control-Allow-Methods: GET");

        try{
            $loggedInUserPosts = $this->post->getLoggedInUserPost(["id" => $this->userId]);
            return $response->json(array("my_posts" => $loggedInUserPosts, "total" => $this->post->getTotalRecords()), 200);
        }catch (Exception $ex)
        {
            return $response->json(array("message" => $ex->getMessage()), 500);
        }
    }
}