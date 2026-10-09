{if $pages > 1}
    <nav class="pager">
        {if $page > 1}
            <a href="/categories/{$categoryId}?sort={$sort}&page={$page - 1}">Назад</a>
        {/if}
        {section name=p loop=$pages}
            {if $smarty.section.p.iteration == $page}
                <span>{$smarty.section.p.iteration}</span>
            {else}
                <a href="/categories/{$categoryId}?sort={$sort}&page={$smarty.section.p.iteration}">{$smarty.section.p.iteration}</a>
            {/if}
        {/section}
        {if $page < $pages}
            <a href="/categories/{$categoryId}?sort={$sort}&page={$page + 1}">Дальше</a>
        {/if}
    </nav>
{/if}
