{{-- NIS REDAS branded page preloader --}}
<style>
    #redas-preloader {
        position: fixed;
        inset: 0;
        z-index: 9999;
        background: #ffffff;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        gap: 1rem;
        transition: opacity 0.45s ease, visibility 0.45s ease;
    }
    #redas-preloader.is-hidden {
        opacity: 0;
        visibility: hidden;
        pointer-events: none;
    }
    .redas-preloader-logo {
        width: 90px;
        height: 90px;
        object-fit: contain;
        animation: redas-preloader-pulse 1.6s ease-in-out infinite;
    }
    .redas-preloader-text {
        color: #006633;
        font-size: 0.9rem;
        font-weight: 600;
        letter-spacing: 0.05em;
        animation: redas-preloader-fade 1.6s ease-in-out infinite;
    }
    @keyframes redas-preloader-pulse {
        0%, 100% { transform: scale(1); opacity: 1; }
        50% { transform: scale(1.08); opacity: 0.85; }
    }
    @keyframes redas-preloader-fade {
        0%, 100% { opacity: 0.6; }
        50% { opacity: 1; }
    }
</style>

<div id="redas-preloader" aria-live="polite" aria-busy="true">
    <img src="{{ asset('nis-logo.png') }}" alt="NIS Logo" class="redas-preloader-logo" fetchpriority="high">
    <div class="redas-preloader-text">Loading NIS-REDAS…</div>
</div>

<script>
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
</script>
