const CACHE_NAME = 'kariana-pwa-v2';
const STATIC_ASSETS = [
  '/',
  '/offline.html',
  '/assets/css/main.css',
  '/assets/images/icon-192.png',
  '/assets/images/icon-512.png',
  '/manifest.json'
];

// 1. Install Event: Pre-cache core shell & offline fallback
self.addEventListener('install', (event) => {
  event.waitUntil(
    caches.open(CACHE_NAME).then((cache) => {
      return cache.addAll(STATIC_ASSETS);
    })
  );
  self.skipWaiting();
});

// 2. Activate Event: Clean up outdated caches
self.addEventListener('activate', (event) => {
  event.waitUntil(
    caches.keys().then((keys) => {
      return Promise.all(
        keys.filter((key) => key !== CACHE_NAME).map((key) => caches.delete(key))
      );
    })
  );
  self.clients.claim();
});

// 3. Fetch Event: Network-first for pages with offline cache fallback, Cache-first for static assets
self.addEventListener('fetch', (event) => {
  const req = event.request;

  // Ignore non-GET and cross-origin requests
  if (req.method !== 'GET' || !req.url.startsWith(self.location.origin)) {
    return;
  }

  // Handle HTML page navigation requests
  if (req.mode === 'navigate' || req.headers.get('accept')?.includes('text/html')) {
    event.respondWith(
      fetch(req)
        .then((networkRes) => {
          // Clone and cache the successfully loaded page
          if (networkRes && networkRes.status === 200) {
            const resClone = networkRes.clone();
            caches.open(CACHE_NAME).then((cache) => cache.put(req, resClone));
          }
          return networkRes;
        })
        .catch(async () => {
          // Check if this page was previously visited and cached
          const cachedRes = await caches.match(req);
          if (cachedRes) {
            return cachedRes;
          }
          // Otherwise return the elegant offline fallback page
          const offlineFallback = await caches.match('/offline.html');
          return offlineFallback || new Response('ইন্টারনেট সংযোগ বিচ্ছিন্ন', {
            status: 503,
            headers: { 'Content-Type': 'text/html; charset=utf-8' }
          });
        })
    );
    return;
  }

  // Handle static assets (CSS, JS, Images, Fonts)
  event.respondWith(
    caches.match(req).then((cachedRes) => {
      if (cachedRes) {
        // Return cached and update in background
        fetch(req).then((networkRes) => {
          if (networkRes && networkRes.status === 200) {
            caches.open(CACHE_NAME).then((cache) => cache.put(req, networkRes));
          }
        }).catch(() => {});
        return cachedRes;
      }
      return fetch(req).then((networkRes) => {
        if (networkRes && networkRes.status === 200) {
          const resClone = networkRes.clone();
          caches.open(CACHE_NAME).then((cache) => cache.put(req, resClone));
        }
        return networkRes;
      });
    })
  );
});
