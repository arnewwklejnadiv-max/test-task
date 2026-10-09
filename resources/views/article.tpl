<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{$article->name}</title>
    <link rel="stylesheet" href="/css/style.css?v={$cssVersion}">
</head>
<body>
{include file="_header.tpl"}
<main class="article-page">
    <article>
        <div class="hero">{include file="_image.tpl"}</div>
        <h1>{$article->name}</h1>
        <p class="lead">{$article->description}</p>
        <p class="meta">
            {if $article->publishedAt}<time>{$article->publishedAt->format('d.m.Y')}</time>{/if}
            <span id="views" data-id="{$article->id}">{$article->views} просмотров</span>
        </p>
        {if $categories}
            <p class="tags">
                {foreach $categories as $category}
                    <a href="/categories/{$category->id}">{$category->name}</a>
                {/foreach}
            </p>
        {/if}
        <div class="text">{$article->text|escape:'html'|nl2br nofilter}</div>
    </article>
    {if $related}
        <section class="category">
            <div class="category-head">
                <h2>Похожие статьи</h2>
            </div>
            <div class="cards">
                {foreach $related as $item}
                    <a class="card" href="/articles/{$item->id}">
                        {include file="_image.tpl" article=$item}
                        <div class="card-body">
                            <h3>{$item->name}</h3>
                            <p>{$item->description}</p>
                            {if $item->publishedAt}<time>{$item->publishedAt->format('d.m.Y')}</time>{/if}
                        </div>
                    </a>
                {/foreach}
            </div>
        </section>
    {/if}
</main>
<script src="/js/article.js"></script>
</body>
</html>
