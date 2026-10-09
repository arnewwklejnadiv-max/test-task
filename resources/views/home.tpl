<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Главная</title>
    <link rel="stylesheet" href="/css/style.css?v=5">
</head>
<body>
{include file="_header.tpl"}
<main>
    <h1>Главная</h1>
    {if $categories}
        <div id="feed">
            {include file="_categories.tpl"}
        </div>
        {if $nextOffset !== null}
            <button type="button" class="button" id="more" data-offset="{$nextOffset}">Еще</button>
            <script src="/js/home.js"></script>
        {/if}
    {else}
        <p class="empty">Категорий со статьями пока нет.</p>
    {/if}
</main>
</body>
</html>
