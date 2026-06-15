const CACHE_NAME = 'cuba-ticket-v3';
const ASSETS = [
  'dashboard.php',
  'manifest.json',
  'icon-192.png',
  'icon-512.png',
  'https://cdn.tailwindcss.com',
  'https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700;900&display=swap'
];

// Instalación: Forzar descarga de nuevos archivos
self.addEventListener('install', e => {
  e.waitUntil(
    caches.open(CACHE_NAME).then(cache => {
      console.log("Instalando nueva versión de caché...");
      return Promise.all(
        ASSETS.map(url => {
          return cache.add(url).catch(err => console.log("Error al cachear:", url));
        })
      );
    })
  );
  self.skipWaiting();
});

// Activación: Borrar versiones v1 y v2
self.addEventListener('activate', e => {
  e.waitUntil(
    caches.keys().then(keys => {
      return Promise.all(
        keys.filter(key => key !== CACHE_NAME).map(key => caches.delete(key))
      );
    })
  );
  return self.clients.claim();
});

// Estrategia: Red primero, si falla, caché
self.addEventListener('fetch', e => {
  e.respondWith(
    fetch(e.request).catch(() => caches.match(e.request))
  );
});