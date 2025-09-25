// sw.js

// A name for our cache
const CACHE_NAME = 'case-reminder-v1';

// A list of all the files we want to cache
const urlsToCache = [
  '/',
  'index.html',
  'https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css'
];

// Listen for the 'install' event
self.addEventListener('install', event => {
  // Wait until the installation is complete
  event.waitUntil(
    // Open the cache
    caches.open(CACHE_NAME)
      .then(cache => {
        console.log('Opened cache');
        // Add all the files from our list to the cache
        return cache.addAll(urlsToCache);
      })
  );
});

// Listen for the 'fetch' event
self.addEventListener('fetch', event => {
  event.respondWith(
    // Look in the cache for a matching request
    caches.match(event.request)
      .then(response => {
        // If we find a match in the cache, return it
        // Otherwise, fetch it from the network
        return response || fetch(event.request);
      })
  );
});