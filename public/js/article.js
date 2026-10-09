(function () {
    const views = document.querySelector('#views');
    if (!views || !views.dataset.id) {
        return;
    }

    fetch('/articles/' + views.dataset.id + '/views', {method: 'POST'})
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
