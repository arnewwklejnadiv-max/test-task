<?php

declare(strict_types=1);

namespace App\Http\Controller;

use App\Domain\ArticleSort;
use App\Domain\Service\ArticleService;
use App\Domain\Service\CategoryService;
use App\Http\Exception\NotFoundException;
use App\Http\Request;
use App\Http\Response;
use App\Http\ViewInterface;

final readonly class CategoryController
{
    public function __construct(
        private CategoryService $categories,
        private ArticleService $articles,
        private ViewInterface $views,
    ) {}

    public function show(Request $request): Response
    {
        $category = $this->categories->find((int) $request->param('id'));
        if ($category === null) {
            throw new NotFoundException();
        }

        $sortQuery = $request->query('sort');
        $pageQuery = $request->query('page');
        $sort = ArticleSort::tryFrom(is_string($sortQuery) ? $sortQuery : '') ?? ArticleSort::Date;
        $page = $this->articles->pageByCategory(
            (int) $category->id,
            $sort,
            is_numeric($pageQuery) ? (int) $pageQuery : 1,
        );

        return Response::html($this->views->render('category', [
            'category' => $category,
            'page' => $page,
            'files' => $this->articles->filesFor($page->items),
            'sort' => $sort,
        ]));
    }
}
