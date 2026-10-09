{foreach $categories as $category}
    <section class="category">
        <div class="category-head">
            <h2>{$category->name}</h2>
            <a class="button" href="/categories/{$category->id}">Все статьи</a>
        </div>
        {if isset($latest[$category->id])}
            <div class="cards">
                {foreach $latest[$category->id] as $article}
                    <a class="card" href="/articles/{$article->id}">
                        {include file="_image.tpl"}
                        <div class="card-body">
                            <h3>{$article->name}</h3>
                            <p>{$article->description}</p>
                            {if $article->publishedAt}<time>{$article->publishedAt->format('d.m.Y')}</time>{/if}
                        </div>
                    </a>
                {/foreach}
            </div>
        {/if}
    </section>
{/foreach}
