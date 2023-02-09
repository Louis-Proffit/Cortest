//configuration de la lecture des champs de la feuille 
const sheet_fid_content = {
    code_barre: {name: 'code_barre', type: 'int', length: 8},
    nom: {name: 'nom', type: 'string', length: 15},
    prenom: {name: 'prenom', type: 'string', length: 11},
    nom_jeune_fille: {name: 'nom_jeune_fille', type: 'string', length: 13},
    niveau_scolaire: {name: 'niveau_scolaire', type: 'choice', length: 1, choice: ['CEP', 'CAP-BEP-BEPC', 'niveau BAC', 'BAC', 'BAC+1', 'BAC+2', 'license ou maitrise', 'ingénieur ou troisème cycle']},
    date_naissance: {name: 'date_naissance', type: 'date', length: 6},
    sexe: {name: 'sexe', type: 'choice', lenght: 1, choice: ['M', 'F']},
    concours: {name: 'concours', type: 'choice', lenght: 1, choice: ['E', 'I', 'R', 'S']},
    SGAP: {name: 'SGAP', type: 'int', length: 2},
    date_examen: {name: 'date_examen', type: 'date', length: 6},
    type_concours: {name: 'type_concours', type: 'int', length: 2},
    batterie: {name: 'batterie', type: 'int', length: 3},
    reserve: {name: 'reserve', type: 'int', length: 5},
    option_1: {name: 'option_1', type: 'int', length: 4},
    option_2: {name: 'option_2', type: 'int', length: 6},
};







//----------------------------------------------------------------------------------------------------------
//----------------------------------------------------------------------------------------------------------
////----------------------------------------------------------------------------------------------------------
////----------------------------------------------------------------------------------------------------------
////----------------------------------------------------------------------------------------------------------
////----------------------------------------------------------------------------------------------------------
////----------------------------------------------------------------------------------------------------------
////----------------------------------------------------------------------------------------------------------
////----------------------------------------------------------------------------------------------------------
//
//
//
//
//
//
//commandes à faire lancer au début par l'utilisateur 

//const p =  await navigator.serial.requestPort();
//await p.open({baudRate: 19200});

//la fonction fonctionne
async function write(port, towrite) {
    const writer = port.writable.getWriter();
    await writer.write(towrite);
    writer.releaseLock();
}

// les commandes

/*
 * V -> version
 * L -> si vide, baisse le bac
 * L -> si plein, lit une feuille
 * G -> vers le bac du bas
 * S -> vers le bac du haut
 */

//fonction read à lancer une fois au début
async function read(port) {
    const reader = port.readable.getReader();
    while (true) {
        //await.read() ne marche pas comme prévu, elle renvoie {value, false} si elle trouve des choses mais reste en pending sinon...
        //les boucles be se terminent pas proprement

        const {value, done} = await reader.read();
        //console.log(done);
        if (done) {
            reader.releaseLock();
            break;
        }
        // value is a Uint8Array.
        //console.log("-!-");
        //console.log(value);
        //console.log(new TextDecoder().decode(value));
        res += new TextDecoder().decode(value);
        store(new TextDecoder().decode(value));
        //mettre ici une ligne permettant de garder la réponse 
    }

}

