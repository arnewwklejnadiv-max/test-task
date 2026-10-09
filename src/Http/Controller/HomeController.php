<?php

declare(strict_types=1);

namespace App\Http\Controller;

use App\Domain\Service\ArticleService;
use App\Domain\Service\CategoryService;
use App\Http\Request;
use App\Http\Response;
use App\Http\ViewInterface;

final readonly class HomeController
{
    public function __construct(
        private CategoryService $categories,
        private ArticleService $articles,
        private ViewInterface $views,
    ) {}

    public function index(Request $request): Response
    {
        $offset = $request->query('offset');
        $feed = $this->categories->withArticles(is_numeric($offset) ? (int) $offset : 0);
        $ids = [];
        foreach ($feed['categories'] as $category) {
            if ($category->id !== null) {
                $ids[] = $category->id;
            }
        }
        $latest = $this->articles->latestPerCategory(3, $ids);
        $data = [
            'categories' => $feed['categories'],
            'latest' => $latest['groups'],
            'files' => $latest['files'],
            'nextOffset' => $feed['nextOffset'],
            'nav' => 'home',
        ];
        $template = $request->query('partial') === '1' ? 'home_more' : 'home';

        return Response::html($this->views->render($template, $data));
    }
}
