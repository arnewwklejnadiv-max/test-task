<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{block name="title"}{/block}</title>
    <link rel="stylesheet" href="/css/style.css?v={$cssVersion}">
</head>
<body>
{include file="_header.tpl"}
{block name="content"}{/block}
{block name="scripts"}{/block}
</body>
</html>
