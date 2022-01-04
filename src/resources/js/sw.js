const PREFIX = 'V1';

const BASE = `${location.protocol}//${location.host}`

const PRECACHERESOURCES = [
    `${BASE}/offline.html`,
    `${BASE}/css/bootstrap/bootstrap.min.css`,
    `${BASE}/css/admin.css`,
    `${BASE}/manifest.json`,
    `${BASE}/reload.js`,
    `${BASE}/img/192.png`,
    `${BASE}/img/default_avatar_male.jpg`,
    `${BASE}/img/default_avatar_female.png`,
    `${BASE}/favicon.ico`,
    `${BASE}/img/bg.png`,
    //'/js/app/editor.js',
   //'/js/lib/actions.js'
];

self.addEventListener('install', (event) => {
    //console.log(`Service worker install event! ${PREFIX}`);
    self.skipWaiting().then(r => {});
    event.waitUntil(
        (async () => {
            const cache = await caches.open(PREFIX);
           await cache.addAll(PRECACHERESOURCES)
        })()
    );
});

self.addEventListener('activate', (event) => {
    self.clients.claim();
    event.waitUntil(
        (async () => {
           const keys = await caches.keys()
            await Promise.all(
                keys.map(key => {
                    if(!key.includes(PREFIX)){
                        return caches.delete(key)
                    }
                })
            )
        })()
    );
    console.log(`Service worker activate event! ${PREFIX}`);
});


self.addEventListener('push', function(event) {
    if (!(self.Notification && self.Notification.permission === 'granted')) {
        return;
    }

   // console.log(payload)
    //const { title, body, actions, icon } = payload
    const sendNotification = ({ title, body, actions, icon }) => {
        return self.registration.showNotification(title, {
            body,
            icon,
            actions
        });
    };


    if (event.data) {
        const data = event.data ? event.data.json() : 'no payload';
        const { title, body, actions, icon } = data
        const payload = { title, body, actions, icon };
        event.waitUntil(sendNotification(payload));
    }
});

self.addEventListener('fetch', event => {
    // console.log('Fetch intercepted for:', event.request.url);
    if(event.request.mode === "navigate"){
        event.respondWith(
            (async () => {
                try {
                    const preloadResponse = await event.preloadResponse
                    if(preloadResponse){
                        return preloadResponse
                    }
                    return await fetch(event.request)
                }catch (e){
                    const cache = await caches.open(PREFIX);
                    return await cache.match('/offline.html');
                }
            })()
        )
    }else if(PRECACHERESOURCES.includes(event.request.url)){
        event.respondWith(caches.match(event.request))
    }
})