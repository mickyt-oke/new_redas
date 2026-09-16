
        /* The return was submitted successfully: clear this directorate's saved
           drafts so the dashboard no longer offers them for resuming. */
        (function () {
            var slug = null;
            try {
                Object.keys(localStorage)
                    .filter(function (k) { return k.indexOf('redas_' + slug + '_draft_') === 0; })
                    .forEach(function (k) { localStorage.removeItem(k); });
            } catch (e) {}
        })();
    