<?php

declare(strict_types=1);

namespace App\Http\Controller;

use App\Domain\Service\ArticleService;
use App\Http\Exception\NotFoundException;
use App\Http\Request;
use App\Http\Response;
use App\Http\Session\ViewSession;
use App\Http\ViewInterface;

final readonly class ArticleController
{
    public function __construct(
        private ArticleService $articles,
        private ViewInterface $views,
        private ViewSession $viewSession,
    ) {}

    public function show(Request $request): Response
    {
        $details = $this->articles->details((int) $request->param('id'));
        if ($details === null) {
            throw new NotFoundException();
        }

        $opened = $this->viewSession->open($request);
        $details['viewToken'] = $opened->viewToken;
        $response = Response::html($this->views->render('article', $details));
        if ($opened->session->id === null || !$opened->cookie) {
            return $response;
        }

        return $response->withCookie(ViewSession::COOKIE, (string) $opened->session->id, ViewSession::LIFETIME);
    }

    public function view(Request $request): Response
    {
        $id = (int) $request->param('id');
        $attempt = $this->viewSession->take($request);
        if (!$attempt->accepted) {
            return Response::text('Запрос отклонён', 403);
        }

        $views = $attempt->fresh ? $this->articles->registerView($id) : $this->articles->views($id);
        if ($views === null) {
            throw new NotFoundException();
        }

        return Response::text((string) $views);
    }
}
