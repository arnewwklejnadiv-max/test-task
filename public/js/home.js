(function () {
    const more = document.querySelector('#more');
    const feed = document.querySelector('#feed');
    if (!more || !feed) {
        return;
    }

    let loading = false;
    more.addEventListener('click', function () {
        if (loading) {
            return;
        }

        loading = true;
        const url = new URL(window.location.href);
        url.searchParams.set('offset', more.dataset.offset || '0');
        url.searchParams.set('partial', '1');
        fetch(url)
            .then(function (response) {
                if (!response.ok) {
                    throw new Error('load failed');
                }

                return response.text();
            })
            .then(function (html) {
                const wrap = document.createElement('div');
                wrap.innerHTML = html;
                const next = wrap.querySelector('#next-batch');
                if (next) {
                    next.remove();
                }
                feed.append(...wrap.childNodes);
                if (next && next.dataset.offset) {
                    more.dataset.offset = next.dataset.offset;
                    loading = false;

                    return;
                }

                more.remove();
            })
            .catch(function () {
                loading = false;
            });
    });
})();
