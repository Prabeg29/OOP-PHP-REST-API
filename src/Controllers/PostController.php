<?php


namespace App\Controllers;

use App\Core\Controller;
use App\Core\Request;
use App\Core\Response;
use App\Middlewares\JWTAuth;
use App\Models\Post;
use Exception;

class PostController extends Controller
{
    protected Post $post;
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
     * @throws Exception
     */
    public function index(Request $request, Response $response)
    {
        header("Access-Control-Allow-Methods: GET");

        try{
            $posts = $this->post->getAllPublishedPosts();
            return $response->json(array("posts" => $posts, "total" => $this->post->getTotalRecords()), 200);
        }catch(Exception $ex){
            return $response->json(array("message" => $ex->getMessage()), 500);
        }
    }

    /**
     * @param Request $request
     * @param Response $response
     * @return false|string
     */
    public function store(Request $request, Response $response)
    {
        header("Access-Control-Allow-Methods: POST");

        $this->json['data'] = $request->getJson();

        try{
            $latestPost = $this->post->createPost([
                'title' => $this->json['data']['title'],
                'slug' => $this->json['data']['slug'],
                'description' => $this->json['data']['description'],
                'imagePath' => $this->json['data']['imagePath'],
                'status' => $this->json['data']['status'] === 'publish' ? 1 : 0,
                'user_id' => $this->userId
            ]);
            return $response->json(array("post" => $latestPost), 201);
        }catch (Exception $ex){
            return $response->json(array("message" => $ex->getMessage()), 500);
        }
    }

    public function show(Request $request, Response $response, $id)
    {
        header("Access-Control-Allow-Methods: GET");

        try{
            $post = $this->post->getPost(['id' => $id]);
            if(is_null($post)){
                return $response->json(array("message" => "Post with id: $id does not exists."), 400);
            }
        }catch(Exception $ex){
            return $response->json(array("message" => $ex->getMessage()), 500);
        }
        return $response->json(array("post" => $post), 200);
    }

    public function update(Request $request, Response $response, $id)
    {
        header("Access-Control-Allow-Methods: PUT");

        try{
            $post = $this->post->getPost(['id'=> $id]);
            if($post['user_id'] !== $this->userId)
            {
                return $response->json(array("message" => "Unauthorized"), 403);
            }

            $this->json['data'] = $request->getJson();

            $updatedPost = $this->post->updatePost([
                'title' => $this->json['data']['title'],
                'slug' => $this->json['data']['slug'],
                'description' => $this->json['data']['description'],
                'imagePath' => $this->json['data']['imagePath'],
                'status' => $this->json['data']['status'] === 'publish' ? 1 : 0,
                'id' => $id
            ]);
            return $response->json(array("post" => $updatedPost), 200);
        }catch (Exception $ex){
            return $response->json(array("message" => $ex->getMessage()), 500);
        }
    }

    public function destroy(Request $request, Response $response, $id)
    {
        header("Access-Control-Allow-Methods: DELETE");

        try{
            $post = $this->post->getPost(['id'=> $id]);
            if($post['user_id'] !== $this->userId)
            {
                return $response->json(array("message" => "Unauthorized"), 403);
            }
            $this->post->deletePost(['id'=> $id]);
            return $response->json(array("message" => "Post deleted"), 200);
        }catch(Exception $ex){
            return $response->json(array("message" => $ex->getMessage()), 500);

        }
    }
}
