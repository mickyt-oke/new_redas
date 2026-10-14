var staticCacheName = "pwa-v" + new Date().getTime();
var filesToCache = [
    '/offline',
    '/css/app.css',
    '/js/app.js',
    '/images/icons/icon-72x72.png',
    '/images/icons/icon-96x96.png',
    '/images/icons/icon-128x128.png',
    '/images/icons/icon-144x144.png',
    '/images/icons/icon-152x152.png',
    '/images/icons/icon-192x192.png',
    '/images/icons/icon-384x384.png',
    '/images/icons/icon-512x512.png',
];

// Cache on install
self.addEventListener("install", event => {
    this.skipWaiting();
    event.waitUntil(
        caches.open(staticCacheName)
            .then(cache => {
                return cache.addAll(filesToCache);
            })
    )
});

// Clear cache on activate
self.addEventListener('activate', event => {
    event.waitUntil(
        caches.keys().then(cacheNames => {
            return Promise.all(
                cacheNames
                    .filter(cacheName => (cacheName.startsWith("pwa-")))
                    .filter(cacheName => (cacheName !== staticCacheName))
                    .map(cacheName => caches.delete(cacheName))
            );
        })
    );
});

// Serve from Cache, but never cache authenticated pages or API calls.
self.addEventListener("fetch", event => {
    const url = new URL(event.request.url);
    const isApi = url.pathname.startsWith('/api/');
    const isAuthenticatedPage = url.pathname.startsWith('/user/')
        || url.pathname.startsWith('/admin/')
        || url.pathname.startsWith('/superadmin/')
        || url.pathname.startsWith('/zonal/')
        || url.pathname.startsWith('/desk-admin/')
        || url.pathname.startsWith('/special-commands/')
        || url.pathname.startsWith('/cgis/')
        || url.pathname.startsWith('/dashboard/')
        || url.pathname.startsWith('/lockscreen');

    // API calls and mutating requests must always hit the network.
    if (isApi || event.request.method !== 'GET') {
        event.respondWith(fetch(event.request).catch(() => caches.match('offline')));
        return;
    }

    // Authenticated HTML pages: network-first so the user never sees a stale
    // cached version after logout, and so the server can enforce session state.
    if (event.request.mode === 'navigate' || isAuthenticatedPage) {
        event.respondWith(
            fetch(event.request)
                .then(response => response)
                .catch(() => caches.match(event.request).then(r => r || caches.match('offline')))
        );
        return;
    }

    // Static assets: cache-first.
    event.respondWith(
        caches.match(event.request)
            .then(response => {
                return response || fetch(event.request);
            })
            .catch(() => {
                return caches.match('offline');
            })
    );
});