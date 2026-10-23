(function () {
    const overlay = document.createElement('div');
    overlay.className = 'page-loading';
    overlay.setAttribute('role', 'status');
    overlay.setAttribute('aria-live', 'polite');
    overlay.setAttribute('aria-hidden', 'true');
    overlay.innerHTML = `
        <div class="page-loading-content">
            <div class="page-loading-indicator">
                <div class="page-loading-logo" aria-hidden="true">
                    <i class="fa-solid fa-computer"></i>
                </div>
                <div class="page-loading-bar" aria-hidden="true"></div>
            </div>
            <p class="page-loading-message">Mohon di tunggu yaa</p>
        </div>
    `;
    document.body.appendChild(overlay);

    let isLoading = false;

    function showLoading() {
        if (isLoading) {
            return;
        }

        isLoading = true;
        overlay.classList.add('is-visible');
        overlay.setAttribute('aria-hidden', 'false');
    }

    document.addEventListener('click', function (event) {
        if (event.defaultPrevented || event.button !== 0 || event.metaKey || event.ctrlKey || event.shiftKey || event.altKey) {
            return;
        }

        const link = event.target.closest('a[href]');
        if (!link || (link.target && link.target !== '_self') || link.hasAttribute('download')) {
            return;
        }

        const destination = new URL(link.href, window.location.href);
        if (
            destination.origin !== window.location.origin ||
            (destination.pathname === window.location.pathname && destination.search === window.location.search)
        ) {
            return;
        }

        showLoading();
    });

    document.addEventListener('submit', function (event) {
        if (!event.defaultPrevented) {
            showLoading();
        }
    });

    window.addEventListener('pageshow', function () {
        isLoading = false;
        overlay.classList.remove('is-visible');
        overlay.setAttribute('aria-hidden', 'true');
    });
})();
