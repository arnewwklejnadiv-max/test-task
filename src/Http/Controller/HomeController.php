<?php

declare(strict_types=1);

namespace App\Http\Controller;

use App\Http\Request;
use App\Http\Response;
use App\Http\ViewInterface;

final readonly class HomeController
{
    public function __construct(private ViewInterface $views) {}

    public function index(Request $request): Response
    {
        return Response::html($this->views->render('home'));
    }
}
