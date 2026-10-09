<?php

declare(strict_types=1);

namespace App\Http\Controller;

use App\Domain\Service\ArticleService;
use App\Http\Exception\NotFoundException;
use App\Http\Request;
use App\Http\Response;
use App\Http\ViewInterface;

final readonly class ArticleController
{
    public function __construct(
        private ArticleService $articles,
        private ViewInterface $views,
    ) {}

    public function show(Request $request): Response
    {
        $details = $this->articles->details((int) $request->param('id'));
        if ($details === null) {
            throw new NotFoundException();
        }

        return Response::html($this->views->render('article', $details));
    }

    public function view(Request $request): Response
    {
        $views = $this->articles->registerView((int) $request->param('id'));
        if ($views === null) {
            throw new NotFoundException();
        }

        return Response::text((string) $views);
    }
}
