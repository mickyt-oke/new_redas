
        (function () {
            var REDAS_PREFILL = null;
            var form = document.querySelector('form[action*="directorates"]');
            if (!form || !REDAS_PREFILL) { return; }

            // Recursively flatten nested objects into bracket-notation field names,
            // e.g. staff[deputy-comptroller-general][male].
            var entries = [];
            (function flatten(prefix, value) {
                if (value === null || value === undefined) { return; }
                if (typeof value === 'object') {
                    Object.keys(value).forEach(function (key) {
                        flatten(prefix ? prefix + '[' + key + ']' : key, value[key]);
                    });
                } else {
                    entries.push([prefix, value]);
                }
            })('', REDAS_PREFILL);

            // Recreate any extra dynamic rows saved with the submission before filling.
            var ensureField = window.redasMakeFieldEnsurer ? window.redasMakeFieldEnsurer() : null;

            entries.forEach(function (pair) {
                var el = null;
                if (ensureField) {
                    el = ensureField(pair[0]);
                } else {
                    try {
                        el = form.querySelector('[name="' + CSS.escape(pair[0]) + '"]');
                    } catch (e) {
                        el = null;
                    }
                }
                if (!el || el.readOnly || el.type === 'file') { return; }
                if (el.type === 'checkbox' || el.type === 'radio') {
                    el.checked = !!pair[1] && pair[1] !== '0';
                } else {
                    el.value = pair[1];
                }
            });

            // Let each page's recompute scripts refresh totals from the restored values.
            form.dispatchEvent(new Event('input', { bubbles: true }));
        })();
    