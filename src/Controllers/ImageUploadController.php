<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Request;
use App\Core\Response;
use App\Middlewares\JWTAuth;

class ImageUploadController extends Controller
{
    private $userId;

    public function __construct()
    {
        $this->userId = JWTAuth::auth();
    }

    public function imageUpload(Request $request, Response $response)
    {
        header("Access-Control-Allow-Methods: POST");

        $allowedExtensions = array('.jpg', '.png', '.jpeg', '.gif');

        $fileExtension = pathinfo($_FILES["file"]["name"])['extension'];
        $filePath = "../public/storage/".time().'_'.basename($_FILES["file"]["name"]);


        // Check file size
        if ($_FILES["fileToUpload"]["size"] > 5000000) {
            $this->json['error']['image'] = "Sorry, your file is too large.";
        }

        // Allow certain file formats
        if(in_array($fileExtension, $allowedExtensions)) {
            $this->json['error']['image'] = "Sorry, only JPG, JPEG, PNG & GIF files are allowed.";
        }

        if(!empty($this->json['error']['image'])){
            return $response->json(array("message" => $this->json['error']['image']), 400);
        }

        if (!move_uploaded_file(
            $_FILES["file"]["tmp_name"],
            $filePath)
        ) {
            $this->json['error']['image'] =  "Sorry, there was an error uploading your file.";
            return $response->json(array("message" => $this->json['error']['image']), 500);
        }

        return $response->json(array("imagePath" => $filePath), 201);
    }
}
