<?php

declare(strict_types=1);

use App\Http\Controller\HomeController;

return [
    ['GET', '/', [HomeController::class, 'index']],
    ['GET', '/home', [HomeController::class, 'index']],
];
