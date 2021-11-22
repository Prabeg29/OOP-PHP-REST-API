<?php

namespace App\Core;

abstract class Controller
{
    protected array $json = [
        'data' => [],
        'error' => [],
    ];
}