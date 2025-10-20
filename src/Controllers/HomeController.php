<?php

namespace App\Controllers;

use App\View;

class HomeController
{
    public function index(): string
    {
        $appName = $_ENV['APP_NAME'] ?? 'МООРС';
        $env     = $_ENV['APP_ENV'] ?? 'local';

        return View::render('home', [
            'title'   => $appName,
            'appName' => $appName,
            'env'     => $env,
        ]);
    }
}