
    (function () {
        const preloader = document.getElementById('redas-preloader');
        if (!preloader) return;

        const start = Date.now();
        const MIN_MS = 700;
        const MAX_MS = 4000;
        let done = false;

        function hide() {
            if (done) return;
            done = true;

            const remaining = Math.max(0, MIN_MS - (Date.now() - start));
            setTimeout(() => {
                preloader.classList.add('is-hidden');
                preloader.setAttribute('aria-busy', 'false');
                setTimeout(() => preloader.remove(), 500);
            }, remaining);
        }

        if (document.readyState === 'complete') {
            hide();
        } else {
            window.addEventListener('load', hide);
            setTimeout(hide, MAX_MS);
        }
    })();
