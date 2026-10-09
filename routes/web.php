<?php

declare(strict_types=1);

use App\Http\Controller\ArticleController;
use App\Http\Controller\CategoryController;
use App\Http\Controller\HomeController;

return [
    ['GET', '/', [HomeController::class, 'index']],
    ['GET', '/home', [HomeController::class, 'index']],
    ['GET', '/categories/{id}', [CategoryController::class, 'show']],
    ['GET', '/articles/{id}', [ArticleController::class, 'show']],
];
