// Register the service worker
if ('serviceWorker' in navigator) {
    // Wait for the 'load' event to not block other work
    window.addEventListener('load', registerServiceWorker);
}
document.onkeydown = function (e) {

    // disable F12 key
    if(e.keyCode === 123) {
        return false;
    }

    // disable I key
    if(e.ctrlKey && e.shiftKey && e.keyCode === 73){
        return false;
    }

    // disable J key
    if(e.ctrlKey && e.shiftKey && e.keyCode === 74) {
        return false;
    }

    // disable U key
    if(e.ctrlKey && e.keyCode === 85) {
        return false;
    }
}

async function registerServiceWorker(){

    try {
        const registration = await navigator.serviceWorker.register('/sw.js');
        let subscription = await registration.pushManager.getSubscription();
        //console.log(subscription)
        if(!subscription){
             subscription =  await registration.pushManager.subscribe({
                userVisibleOnly: true,
                applicationServerKey: await getPublicKey(),
            })
        }
        await saveSubscription(subscription)
        console.log('Service worker registered! 😎', registration);
    } catch (err) {
        console.log('😥 Service worker registration failed: ', err);
    }
}


async function askPermission(){
    const permission =  await Notification.requestPermission()
    console.log(permission)
}


async function getPublicKey() {
    const {key} = await fetch('/push/key').then(data=>data.json())
    return key;
}

/**
 *
 * @param subscription
 * @returns {Promise<void>}
 */
async function saveSubscription(subscription){

   await fetch('/push/subscriber', {
        method: 'POST',
        headers : {
            'Content-Type': 'application/json',
            Accept: 'application/json'
        },
        body: JSON.stringify({subscription, ...window.csrf})
    })
}