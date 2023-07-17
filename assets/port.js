/* 
 * Gestion de la connection avec le lecteur optique
 */

let port = null;
const BAUD_RATE = 19200

global.connect = async function (callback) {

    if (debugPort) {
        return callback();
    } else {


        port = await navigator.serial.requestPort();
        await port.open({baudRate: BAUD_RATE});
        await read();

        callback();
    }


}

global.tryConnexion = async function (toDo) {
    const test = await get('V');
    if (test.match(/\x01\x02.*r\n\x03\x04/) !== null) {
        toDo();
    }
}

//stocke les réponses du lecteur
let answer_cache = "";

global.read = async function () {
    //une fois lancée, la fonction écoute en permanence le port et écrit sur le cache de réponse
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

global.tell = async function (commande) {

    if (debugPort) {

        console.log("tell = " + commande)

    } else {

        answer_cache = "";
        console.log('Request : ' + commande);

        //envoi de la requête
        const writer = port.writable.getWriter();
        await writer.write(new TextEncoder().encode(commande));
        writer.releaseLock();

    }

}

global.timeout = function (ms) {
    return new Promise(resolve => setTimeout(resolve, ms));
}

global.get = async function (commande) {

    //pour le debug :
    //debugPort
    if (debugPort) {
        console.log('ask = ' + commande);
        let r = window.prompt("Résultat du " + commande, "");
        r = r.replaceAll('\\x15', String.fromCharCode(21));
        r = r.replaceAll('\\r', String.fromCharCode(13));
        r = r.replaceAll('\\n', String.fromCharCode(10));
        r = r.replaceAll('\\x03', String.fromCharCode(3));
        r = r.replaceAll('\\x04', String.fromCharCode(4));
        r = r.replaceAll('\\x02', String.fromCharCode(2));
        r = r.replaceAll('\\x01', String.fromCharCode(1));
        console.log('get = ' + r);
        return r;
    } else {

        answer_cache = "";
        console.log('Request : ' + commande);

        //Envoi de la requête
        const writer = port.writable.getWriter();
        await writer.write(new TextEncoder().encode(commande));
        writer.releaseLock();

        //on écoute la réponse (pas trop longtemps quand même...)

        for (let n = 0; n < 300; n++) {
            await timeout(10);
            if (answer_cache.slice(-1) === '\x04' || answer_cache.slice(-1) === '\x03') {
                const cache = Object.assign({}, {text: answer_cache}).text;
                await timeout(40);
                if (answer_cache === cache) {
                    console.log('Response : ');
                    console.log({answer_cache});
                    return answer_cache;
                }
            }
        }
        return answer_cache;

    }
}