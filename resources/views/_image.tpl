{assign var="imageFile" value=null}
{if $article->fileId !== null && isset($files[$article->fileId])}
    {assign var="imageFile" value=$files[$article->fileId]}
{/if}
{if $imageFile}
    {assign var="imageSrc" value="{$imageFile->path}/{$imageFile->name}"}
    {if $imageFile->path|substr:0:4 != 'http'}
        {assign var="imageSrc" value="/{$imageSrc}"}
    {/if}
    <img class="cover" src="{$imageSrc}" alt="{$article->name}">
{else}
    <img class="cover" src="/images/placeholder.svg" alt="{$article->name}">
{/if}
