{extends file="layout.tpl"}
{block name="title"}Главная{/block}
{block name="content"}
<main>
    <h1>Главная</h1>
    {if $categories}
        <div id="feed">
            {include file="_categories.tpl"}
        </div>
        {if $nextOffset !== null}
            <button type="button" class="button" id="more" data-offset="{$nextOffset}">Еще</button>
        {/if}
    {else}
        <p class="empty">Категорий со статьями пока нет.</p>
    {/if}
</main>
{/block}
{block name="scripts"}
{if $nextOffset !== null}
    <script src="/js/home.js"></script>
{/if}
{/block}
