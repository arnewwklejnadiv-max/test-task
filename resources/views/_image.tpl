{assign var="imageFile" value=null}
{if $article->fileId !== null && isset($files[$article->fileId])}
    {assign var="imageFile" value=$files[$article->fileId]}
{/if}
{if $imageFile}
    <img class="cover" src="/{$imageFile->path}/{$imageFile->name}" alt="{$article->name}">
{else}
    <img class="cover" src="/images/placeholder.svg" alt="{$article->name}">
{/if}
