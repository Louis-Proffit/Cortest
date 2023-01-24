/* 
 * Gestion de la connection avec le lecteur optique
 */

var port = null;

async function connect() {
    port = await navigator.serial.requestPort();
    await port.open({baudRate: 19200});
    read();
}

//stocke les réponses du lecteur
var answer_cache = "";

async function read() {
    //une fois lancée, la fonction écoute en permanance le port et écrit sur le cache de réponse
    const reader = port.readable.getReader();
    while (true) {
        const {value, done} = await reader.read();
        if (done) {
            reader.releaseLock();
            break;
        }
        answer_cache += new TextDecoder().decode(value);
    }
}

async function tell(commande) {
    //on vide le cache
    answer_cache = "";

    //envoie de la requete
    const writer = port.writable.getWriter();
    await writer.write(new TextEncoder().encode(commande));
    writer.releaseLock();

}

async function get(commande) {
    //on vide le cache
    answer_cache = "";

    //envoie de la requete
    const writer = port.writable.getWriter();
    await writer.write(new TextEncoder().encode(commande));
    writer.releaseLock();

    //on écoute la réponse (pas trop longtemps quand même...)

    for (let n = 0; n < 300; n++) {
        await timeout(10);
        if (answer_cache.indexOf(end) !== -1) {
            return answer_cache;
        }
    }
    return false;
}
