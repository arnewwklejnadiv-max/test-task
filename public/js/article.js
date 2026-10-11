(function () {
    const views = document.querySelector('#views');
    const token = document.querySelector('meta[name="csrf-token"]');
    if (!views || !views.dataset.id || !token || token.content === '') {
        return;
    }

    fetch('/articles/' + views.dataset.id + '/views', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': token.content,
        },
    })
        .then(function (response) {
            if (!response.ok) {
                throw new Error('view failed');
            }

            return response.text();
        })
        .then(function (count) {
            views.textContent = count + ' просмотров';
        });
})();
