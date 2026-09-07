self.addEventListener('push', function (event) {
    let data = {};

    try {
        data = event.data ? event.data.json() : {};
    } catch (e) {
        data = {
            title: 'HF Parfum',
            body: event.data ? event.data.text() : 'Ada pesanan baru.',
        };
    }

    const title = data.title || 'HF Parfum';

    const options = {
        body: data.body || 'Ada pesanan baru.',
        icon: '/images/logo-hf.png',
        badge: '/images/logo-hf.png',
        data: {
            url: data.url || '/admin/pesanan',
        },
    };

    event.waitUntil(
        self.registration.showNotification(title, options)
    );
});

self.addEventListener('notificationclick', function (event) {
    event.notification.close();

    const url = event.notification.data?.url || '/admin/pesanan';

    event.waitUntil(
        clients.matchAll({
            type: 'window',
            includeUncontrolled: true,
        }).then(function (clientList) {
            for (const client of clientList) {
                if ('focus' in client) {
                    client.navigate(url);
                    return client.focus();
                }
            }

            if (clients.openWindow) {
                return clients.openWindow(url);
            }
        })
    );
});