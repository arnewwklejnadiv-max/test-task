<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{$category->name}</title>
    <link rel="stylesheet" href="/css/style.css?v={$cssVersion}">
</head>
<body>
{include file="_header.tpl"}
<main>
    <header class="page-head">
        <h1>{$category->name}</h1>
        <p>{$category->description}</p>
    </header>
    <div class="sort">
        <a href="/categories/{$category->id}?sort=date"{if $sort->value == 'date'} class="is-active"{/if}>По дате</a>
        <a href="/categories/{$category->id}?sort=views"{if $sort->value == 'views'} class="is-active"{/if}>По просмотрам</a>
    </div>
    {if $page->items}
        <div class="rows">
            {foreach $page->items as $article}
                <a class="row" href="/articles/{$article->id}">
                    {include file="_image.tpl" files=$files}
                    <div>
                        <h2>{$article->name}</h2>
                        <p>{$article->description}</p>
                        <p class="meta">
                            {if $article->publishedAt}<time>{$article->publishedAt->format('d.m.Y')}</time>{/if}
                            <span>{$article->views} просмотров</span>
                        </p>
                    </div>
                </a>
            {/foreach}
        </div>
        {include file="_pagination.tpl" categoryId=$category->id page=$page->page pages=$page->pages() sort=$sort->value}
    {else}
        <p class="empty">Статей пока нет.</p>
    {/if}
</main>
</body>
</html>
