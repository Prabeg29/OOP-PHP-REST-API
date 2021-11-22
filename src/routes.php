<?php

use App\Core\Router;
use App\Controllers\AuthController;
use App\Controllers\PostController;
use App\Controllers\UserPostController;
use App\Controllers\ImageUploadController;

Router::post('/api/v1/users/register', [AuthController::class, 'register']);
Router::post('/api/v1/users/login', [AuthController::class, 'login']);

Router::get('/api/v1/posts', [PostController::class, 'index']);
Router::post('/api/v1/posts', [PostController::class, 'store']);
Router::get('/api/v1/posts/{$id}', [PostController::class, 'show']);
Router::put('/api/v1/posts/{$id}', [PostController::class, 'update']);
Router::delete('/api/v1/posts/{$id}', [PostController::class, 'destroy']);


Router::get('/api/v1/my-posts', [UserPostController::class, 'index']);

Router::post('/api/v1/image-upload', [ImageUploadController::class, 'imageUpload']);
