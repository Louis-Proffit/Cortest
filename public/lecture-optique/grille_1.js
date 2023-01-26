/* 
 * Lecture de la grille du cahier des charges
 */

class GrilleManager {

//représente une feuille FID et comment elle doit être lue
    static contentFID = {
        code_barre: {name: 'Code barre', type: 'number', length: 8, regex: /^[0-9]{8}$/},
        nom: {name: 'Nom', type: 'string', length: 15, regex: /^[A-Z]+[\s]*$/},
        prenom: {name: 'Prénom', type: 'string', length: 11, regex: /^[A-Z]+[\s]*$/},
        nom_jeune_fille: {name: 'Nom de jeune fille', type: 'string', length: 12, regex: /^[A-Z]*[\s]*$/},
        niveau_scolaire: {name: 'Niveau scolaire', type: 'choice', length: 1, regex: /^[1-8]$/, choice: [
                {read: '1', print: 'CEP', store: 'CEP'},
                {read: '2', print: 'CAP-BEP-BEPC', store: 'CAP-BEP-BEPC'},
                {read: '3', print: 'niveau BAC', store: 'niveau BAC'},
                {read: '4', print: 'BAC', store: 'BAC'},
                {read: '5', print: 'BAC+1', store: 'BAC+1'},
                {read: '6', print: 'BAC+2', store: 'BAC+2'},
                {read: '7', print: 'license ou maitrise', store: 'license ou maitrise'},
                {read: '8', print: 'ingénieur ou troisème cycle', store: 'ingénieur ou troisème cycle'},
            ]},
        date_naissance: {name: 'Date de naissance', type: 'date', length: 6, regex: /[0-3][0-9][0-1][0-9]{3}/},
        sexe: {name: 'Sexe', type: 'choice', length: 1, regex: /^[1-2]$/, choice: [
                {read: '1', print: 'M', store: 'M'},
                {read: '2', print: 'F', store: 'F'},
            ]},
        concours: {name: 'Concours', type: 'choice', length: 1, regex: /^[EIRS]$/, choice: [
                {read: 'E', print: 'E', store: 'E'},
                {read: 'I', print: 'I', store: 'I'},
                {read: 'R', print: 'R', store: 'R'},
                {read: 'S', print: 'S', store: 'S'},
            ]},
        SGAP: {name: 'SGAP', type: 'number', length: 2, regex: /^[0-9]{2}$/},
        date_examen: {name: 'Date d\'examen', type: 'date', length: 6, regex: /[0-3][0-9][0-1][0-9]{3}/},
        type_concours: {name: 'Type concours', type: 'number', length: 2, regex: /^[0-9]{2}$/},
        batterie: {name: 'Batterie', type: 'number', length: 3, regex: /^[0-9]{3}$/},
        reserve: {name: 'Reservé', type: 'number', length: 5, regex: /^[0-9]{5}$/},
        option_1: {name: 'Option 1', type: 'number', length: 4, regex: /^[0-9]{4}$/},
        option_2: {name: 'Option 2', type: 'number', length: 6, regex: /^[0-9]{6}$/},
    };
    constructor(request, nbQuestions) {

        /* request décrit ce qui est attendu pour cette session
         * elle doit être de la forme
         * 
         * request = {
         code_barre: {asked: true, expected: false, print: true},
         nom: {asked: true, expected: false, print: true},
         ...
         }
         */

        this.request = request;
        this.FIDs = [];
        this.QCMs = [];
        this.nbQuestions = nbQuestions;
    }

    getGridConfig() {
//renvoie la configuration pour le tableau AG-GRID
        var columnDefs = [];
        for (var field in GrilleManager.contentFID) {
            var ligne = GrilleManager.contentFID[field];
            if (this.request[field].print) {
                switch (ligne.type) {
                    case 'date':
                        columnDefs.push({field: field, headerName: ligne.name, type: 'dateColumn'});
                        break;
                    case 'choice':
                        var options = [];
                        for (var i in ligne.choice) {
                            options.push(ligne.choice[i].store);
                        }
                        columnDefs.push({field: field, headerName: ligne.name, cellEditor: 'agSelectCellEditor', cellEditorParams: {values: options}});
                        break;
                    default:
                        columnDefs.push({field: field, headerName: ligne.name});
                }
            }
        }

        var gridOptions = {
            columnDefs: columnDefs,
            rowData: this.FIDs,
            defaultColDef: {
                sortable: true,
                filter: true,
                editable: true
            },
            columnTypes: {
                dateColumn: {
                    filter: 'agDateColumnFilter',
                    //filterParams: {comparator: myDateComparator},
                    suppressMenu: true
                }
            },
        };
        this.gridOptions = gridOptions;
        //console.log(gridOptions);
        return gridOptions;
    }

    storeFID(fid) {
//ajoute une fid à la liste des fid traités
        this.FIDs = this.FIDs.concat([fid])
        this.gridOptions.api.setRowData(this.FIDs);
        $("#nb-fid-lues").text(parseInt($("#nb-fid-lues").text())+1);
    }

    hasFID(code_barre) {
//vérifie si une fid a été traitée
        return this.FIDs.findIndex((e) => e.code_barre === code_barre) !== -1;
    }

    removeFID(code_barre) {
//supprime une fid de la liste des fids traités
        this.FIDs = this.FIDs.splice(this.FIDs.findIndex((e) => e.code_barre === code_barre), 1);
        $("#nb-fid-lues").text(parseInt($("#nb-fid-lues").text())-1);
    }

    hasQCMs(code_barre) {
//vérifie si un qcm a été traité
        return this.QCMs.findIndex((e) => e.code_barre === code_barre) !== -1;
    }

    storeQCM(qcm) {
//ajoute une fid à la liste des fid traités
        this.QCMs = this.QCMs.concat([qcm]);
        $("#nb-qcm-lues").text(parseInt($("#nb-qcm-lues").text())+1);
    }

    removeQCM(code_barre) {
//supprime une fid de la liste des fids traités
        this.QCMs = this.QCMs.splice(this.QCMs.findIndex((e) => e.code_barre === code_barre), 1);
        $("#nb-qcm-lues").text(parseInt($("#nb-qcm-lues").text())-1);
    }

    correctFID(fid) {
        var forms = [];
        const blanck = '_';
        const unknown = '?';
        const not_asked = 'not_asked';
        const siecleCorrection = 20;
        const anneeCorrection = 23;
        for (var field in GrilleManager.contentFID) {
            var ligne = GrilleManager.contentFID[field];
            var requestField = this.request[field];
            if (requestField.asked) {
                //si le champs est demandé
                if (fid[field].match(ligne.regex)) {
                    //si la lecture est coérente
                    if (ligne.type === 'choice') {
                        for (var i in ligne.choice) {
                            if (ligne.choice[i].read === fid[field]) {
                                fid[field] = ligne.choice[i].store;
                            }
                        }
                    }
                    if (ligne.type === 'date') {
                        var read = fid[field];
                        if (field === "date_naissance") {
                            if (parseInt(read[4] + read[5]) > anneeCorrection) {
                                fid[field] = (siecleCorrection - 1).toString() + read[4] + read[5] + '-' + read[2] + read[3] + '-' + read[0] + read[1];
                            } else {
                                fid[field] = siecleCorrection + read[4] + read[5] + '-' + read[2] + read[3] + '-' + read[0] + read[1];
                            }

                        } else {
                            fid[field] = siecleCorrection + read[4] + read[5] + '-' + read[2] + read[3] + '-' + read[0] + read[1];
                        }
                    }

                    if (requestField.expected !== false) {
                        //si une valeur particulière était attendu
                        if (fid[field] !== requestField.expected) {
                            //si ce n'est pas celle fournie
                            forms.push(formConfirm(field, ligne.name, fid[field], requestField.expected,
                                    function () {
                                        fid[field] = my.request[field].expected;
                                    }, function () {}));

                        }
                    }

                } else {
                    //si la lecture est incohérente

                    if (requestField.expected !== false) {
                        forms.push(formConfirm(field, ligne.name, fid[field], requestField.expected,
                                function () {
                                    fid[field] = my.request[field].expected;
                                }, function () {}));


                    }

                    if (ligne.type === 'choice') {
                        forms.push(formSelect(field, ligne.name, ligne.choice, function (r) {
                            fid[field] = r;
                        }, function () {
                            fid[field] = blanck;
                        }, function () {
                            fid[field] = unknown;
                        }));
                    } else {
                        forms.push(formInput(field, ligne.name, fid[field], ligne.type, function (r) {
                            fid[field] = r;
                        }, function () {
                            fid[field] = blanck;
                        }, function () {
                            fid[field] = unknown;
                        }));
                    }
                }
            } else {
                //si le champs n'est pas demandé
                fid[field] = not_asked;
            }

        }
        //la lecture est terminée, on demande les corrections nécessaires
        if (forms.length > 0) {
            //tell('S');
            var my = this;
            askFID(fid.code_barre, forms, function () {
                for (var i in forms) {
                    var line = forms[i];
                    field = line.field;
                    line.action();
                }
                my.storeFID(fid);
                my.readFIDs();
            }, function () {my.readFIDs();});
        } else {
            //tell('G');
            this.storeFID(fid);
            return this.readFIDs()
        }


    }

    correctQCM(code_barre, qcm) {
        const blanck = '_';
        const unknown = '?';
        const corresp = {'A': 'A', 'B': 'B', 'D': 'C', 'H': 'D', 'P': 'E'};
        var toCorrect = [];
        for (let i = 0; i < this.nbQuestions; i++) {
            if (['A', 'B', 'D', 'H', 'P'].includes(qcm[i])) {
                qcm[i] = corresp[qcm[i]];
            } else {
                toCorrect.push(i);
            }
        }
        if (toCorrect.length > 0) {
            //tell('S');
            var my = this;
            askQCM(code_barre, toCorrect, function (rep) {
                for (var j in rep) {
                    qcm[rep[j].question] = rep[j].response;
                }
                my.storeQCM(qcm);
                my.readQCMs();
            }, function () { my.readQCMs();}, '_', '?');
        } else {
            //tell('G');
            this.storeQCM(qcm);
            this.readQCMs();

        }

    }

    readFID(text) {
        var fid = {};
        var cursor = 0;
        for (var field in GrilleManager.contentFID) {
            var ligne = GrilleManager.contentFID[field];
            var step = ligne.length;
            fid[field] = text.slice(cursor, cursor + step);
            cursor += step;
        }
        this.correctFID(fid);
    }

    readQCM(text) {
        var qcm = [];
        for (let i = 0; i < this.nbQuestions; i++) {
            qcm[i] = text[8 + i];
        }
        this.correctQCM(text.slice(0, 8), qcm);
    }

    static codesErreurs = [
        {sequence: "00003 Erreur marques de cadrage", message: "Marques de cadrage"},
        {sequence: "00003 Erreur nombre marques horloges", message: "Nombre marques horloges"},
    ];

    async readFIDs() {
        $("#spinner-fid").show();
        //var rep = await get('L');
        var rep = "\x15  00003 Erreur type de feuille\x15  00003 Erreur type de feuille\r\n\x03\x01\x023250011018500110TEST TPPTLS    T PPTSA                70506942 150401??99003     1007      090\r\n\x03\x04";
        

        const bac_vide = "\x1506\r\n\x03";

        if (rep.includes(bac_vide)) {
            //on n'a plus de page à lire
            //on revoie une commande L pour baisser le bac
            
            //tell('L')
            
            //fin de lecture
            console.log("Fin de lecture");
            $("#spinner-fid").hide();
            return true;
        }

        //si il y a effectivement un epage à lire
        //var rep = "\x15  00003 Erreur type de feuille\x15  00003 Erreur type de feuille\r\n\x03\x01\x023250011018500110TEST TPPTLS    T PPTSA                70506942 150401??99003     1007      090\r\n\x03\x04";
        for (var i in GrilleManager.codesErreurs) {
            var erreur = GrilleManager.codesErreurs[i];
            var my = this;
            if (rep.includes(erreur.sequence)) {
                return tellFatalError(erreur.message, "Lire la page suivante", function () {my.readFIDs();});
            }
        }
        var regex = /\x01\x02[0-9]{8}(.{83})[0-9]{3}\r\n\x03\x04/;
        var match = rep.match(regex);
        if (match === null) {
            var my = this;
            return tellFatalError("Réponse reçue : "+rep, "Lire la page suivante", function () { my.readFIDs();});
        } else {
            var expl = match[1];
            return this.readFID(expl);
        }
    }

    readQCMs() {
        $("#spinner-qcm").show();
        //var rep = await get('L');
        

        const bac_vide = "\x1506\r\n\x03";

        if (rep.includes(bac_vide)) {
            //on n'a plus de page à lire
            //on revoie une commande L pour baisser le bac
            
            //tell('L')
            
            //fin de lecture
            console.log("Fin de lecture");
            $("#spinner-qcm").hide();
            return true;
        }

        //si il y a effectivement un epage à lire
        //var rep = "\x15  00003 Erreur type de feuille\x15  00003 Erreur type de feuille\r\n\x03\x01\x023250011018500110TEST TPPTLS    T PPTSA                70506942 150401??99003     1007      090\r\n\x03\x04";
        for (var i in GrilleManager.codesErreurs) {
            var erreur = GrilleManager.codesErreurs[i];
            var my = this;
            if (rep.includes(erreur.sequence)) {
                return tellFatalError(erreur.message, "Lire la page suivante", function () {my.readFIDs();});
            }
        }
        var regex = /\x01\x02[0-9]{8}(.{648})[0-9]{3}\r\n\x03\x04/;
        var match = rep.match(regex);
        if (match === null) {
            var my = this;
            return tellFatalError("Inconnue", "Lire la page suivante", function () { my.readFIDs();});
        } else {
            var expl = match[1];
            return this.readQCM(expl);
        }

    }
}
