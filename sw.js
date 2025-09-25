// sw.js

const CACHE_NAME = 'case-reminder-v2'; // Updated cache name
const urlsToCache = [
  '/',
  'index.html',
  'data/appCaseDate.json', // Cache the data file
  'https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css'
];

// Install event: cache core assets
self.addEventListener('install', event => {
  event.waitUntil(
    caches.open(CACHE_NAME)
      .then(cache => {
        console.log('Opened cache');
        return cache.addAll(urlsToCache);
      })
  );
});

// Activate event: clean up old caches
self.addEventListener('activate', event => {
  event.waitUntil(
    caches.keys().then(cacheNames => {
      return Promise.all(
        cacheNames.filter(cacheName => cacheName !== CACHE_NAME)
                  .map(cacheName => caches.delete(cacheName))
      );
    })
  );
});


// Fetch event: serve from cache first, then network
self.addEventListener('fetch', event => {
  event.respondWith(
    caches.match(event.request)
      .then(response => {
        // Cache hit - return response
        if (response) {
          return response;
        }
        // Not in cache - fetch from network
        return fetch(event.request);
      })
  );
});


// === Push Notification Event Listener ===
self.addEventListener('push', event => {
  console.log('Push event received.');
  
  let notificationData = {};
  try {
    // Attempt to parse the data payload from the server
    notificationData = event.data.json();
  } catch (e) {
    console.log('No JSON payload, using default message.');
    notificationData = {
      title: 'มีการแจ้งเตือนใหม่',
      body: 'มีข้อมูลคดีที่น่าสนใจอัปเดต',
    };
  }

  const title = notificationData.title;
  const options = {
    body: notificationData.body,
    icon: 'assets/icons/crimc-alert-icon-192x192.png',
    badge: 'assets/icons/crimc-alert-icon-badge.png', // For Android
    vibrate: [200, 100, 200]
  };

  event.waitUntil(
    self.registration.showNotification(title, options)
  );
});

// Notification click event
self.addEventListener('notificationclick', event => {
    event.notification.close();
    event.waitUntil(
        clients.openWindow('/') // Open the app when notification is clicked
    );
});