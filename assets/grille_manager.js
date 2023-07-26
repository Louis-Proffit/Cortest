/* 
 * Lecture de la grille du cahier des charges
 */

export class GrilleManager {

//représente une feuille FID et comment elle doit être lue
    static contentFID = {
        code_barre: {name: 'Code barre', type: 'number', length: 8, regex: /^.{8}$/},
        nom: {name: 'Nom', type: 'string', length: 15, regex: /^[A-Z]+[\s]*$/},
        prenom: {name: 'Prénom', type: 'string', length: 11, regex: /^[A-Z]+[\s]*$/},
        nom_jeune_fille: {name: 'Nom de jeune fille', type: 'string', length: 12, regex: /^[A-Z]*[\s]*$/},
        niveau_scolaire: {
            name: 'Niveau scolaire', type: 'choice', length: 1, regex: /^[1-8]$/, choice: [
                {read: '1', print: 'CEP', store: 'CEP'},
                {read: '2', print: 'CAP-BEP-BEPC', store: 'CAP-BEP-BEPC'},
                {read: '3', print: 'niveau BAC', store: 'niveau BAC'},
                {read: '4', print: 'BAC', store: 'BAC'},
                {read: '5', print: 'BAC+1', store: 'BAC+1'},
                {read: '6', print: 'BAC+2', store: 'BAC+2'},
                {read: '7', print: 'license ou maitrise', store: 'license ou maitrise'},
                {read: '8', print: 'ingénieur ou troisème cycle', store: 'ingénieur ou troisème cycle'},
            ]
        },
        date_naissance: {name: 'Date de naissance', type: 'date', length: 6, regex: /[0-3][0-9][0-1][0-9]{3}/},
        sexe: {
            name: 'Sexe', type: 'choice', length: 1, regex: /^[1-2]$/, choice: [
                {read: '1', print: 'M', store: 1},
                {read: '2', print: 'F', store: 2},
            ]
        },
        concours: {
            name: 'Concours', type: 'choice', length: 1, regex: /^[EIRS]$/, choice: [
                {read: 'E', print: 'E', store: 'E'},
                {read: 'I', print: 'I', store: 'I'},
                {read: 'R', print: 'R', store: 'R'},
                {read: 'S', print: 'S', store: 'S'},
            ]
        },
        SGAP: {name: 'SGAP', type: 'number', length: 2, regex: /^[0-9]{2}$/},
        date_examen: {name: 'Date d\'examen', type: 'date', length: 6, regex: /[0-3][0-9][0-1][0-9]{3}/},
        type_concours: {name: 'Type concours', type: 'number', length: 2, regex: /^[0-9]{2}$/},
        batterie: {name: 'Batterie', type: 'number', length: 3, regex: /^[0-9]{3}$/},
        reserve: {name: 'Reservé', type: 'number', length: 5, regex: /^[0-9]{5}$/},
        option_1: {name: 'Option 1', type: 'number', length: 4, regex: /^[0-9]{4}$/},
        option_2: {name: 'Option 2', type: 'number', length: 6, regex: /^[0-9]{6}$/},
    };


    /**
     *
     * @param {CortestPort} port
     * @param request
     * @param questions
     * @param niveauxScolaires
     */
    constructor(port, request, questions, niveauxScolaires) {

        /* Request décrit ce qui est attendu pour cette session
         * elle doit être de la forme
         * 
         * request = {
         code_barre: {asked: true, expected: false, print: true},
         nom: {asked: true, expected: false, print: true},
         ...
         }
         */

        this.port = port
        this.contentFID = {
            code_barre: {name: 'Code barre', type: 'number', length: 8, regex: /^.{8}$/},
            nom: {name: 'Nom', type: 'string', length: 15, regex: /^[A-Z]+[\s]*$/},
            prenom: {name: 'Prénom', type: 'string', length: 11, regex: /^[A-Z]+[\s]*$/},
            nom_jeune_fille: {name: 'Nom de jeune fille', type: 'string', length: 12, regex: /^[A-Z]*[\s]*$/},
            niveau_scolaire: {
                name: 'Niveau scolaire', type: 'choice', length: 1, regex: /^[1-8]$/, choice: [
                    {read: '1', print: 'CEP', store: 'CEP'},
                    {read: '2', print: 'CAP-BEP-BEPC', store: 'CAP-BEP-BEPC'},
                    {read: '3', print: 'niveau BAC', store: 'niveau BAC'},
                    {read: '4', print: 'BAC', store: 'BAC'},
                    {read: '5', print: 'BAC+1', store: 'BAC+1'},
                    {read: '6', print: 'BAC+2', store: 'BAC+2'},
                    {read: '7', print: 'license ou maitrise', store: 'license ou maitrise'},
                    {read: '8', print: 'ingénieur ou troisème cycle', store: 'ingénieur ou troisème cycle'},
                ]
            },
            date_naissance: {name: 'Date de naissance', type: 'date', length: 6, regex: /[0-3][0-9][0-1][0-9]{3}/},
            sexe: {
                name: 'Sexe', type: 'choice', length: 1, regex: /^[1-2]$/, choice: [
                    {read: '1', print: 'M', store: 1},
                    {read: '2', print: 'F', store: 2},
                ]
            },
            concours: {
                name: 'Concours', type: 'choice', length: 1, regex: /^[EIRS]$/, choice: [
                    {read: 'E', print: 'E', store: 'E'},
                    {read: 'I', print: 'I', store: 'I'},
                    {read: 'R', print: 'R', store: 'R'},
                    {read: 'S', print: 'S', store: 'S'},
                ]
            },
            SGAP: {name: 'SGAP', type: 'number', length: 2, regex: /^[0-9]{2}$/},
            date_examen: {name: 'Date d\'examen', type: 'date', length: 6, regex: /[0-3][0-9][0-1][0-9]{3}/},
            type_concours: {name: 'Type concours', type: 'number', length: 2, regex: /^[0-9]{2}$/},
            batterie: {name: 'Batterie', type: 'number', length: 3, regex: /^[0-9]{3}$/},
            reserve: {name: 'Reservé', type: 'number', length: 5, regex: /^[0-9]{5}$/},
            option_1: {name: 'Option 1', type: 'number', length: 4, regex: /^[0-9]{4}$/},
            option_2: {name: 'Option 2', type: 'number', length: 6, regex: /^[0-9]{6}$/},
        };
        this.contentFID.niveau_scolaire.choice = niveauxScolaires;

        this.request = request;
        this.FIDs = [];
        this.QCMs = [];
        this.nbQuestions = 640;
        this.questions = questions;
        this.noCodeBarreFID = 1;
        this.noCodeBarreQCM = 1;
        this.codesAppaires = [];
    }

    getGridConfig() {
//renvoie la configuration pour le tableau AG-GRID
        const columnDefs = [];
        for (let field in this.contentFID) {
            const ligne = this.contentFID[field];
            if (this.request[field].print) {
                switch (ligne.type) {
                    case 'date':
                        columnDefs.push({field: field, headerName: ligne.name, type: 'dateColumn'});
                        break;
                    case 'choice':
                        const options = [];
                        for (let i in ligne.choice) {
                            options.push(ligne.choice[i].store);
                        }
                        columnDefs.push({
                            field: field,
                            headerName: ligne.name,
                            cellEditor: 'agSelectCellEditor',
                            cellEditorParams: {values: options}
                        });
                        break;
                    default:
                        columnDefs.push({field: field, headerName: ligne.name});
                }
            }
        }

        const gridOptions = {
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

        return gridOptions;
    }

    storeFID(fid) {
//ajoute une fid à la liste des fid traités
        this.FIDs = this.FIDs.concat([fid])
        this.gridOptions.api.setRowData(this.FIDs);
        if (this.hasQCM(fid.code_barre)) {
            this.codesAppaires.push(fid.code_barre);
            $("#nb-appaires").text(parseInt($("#nb-appaires").text()) + 2);
        }

        $("#nb-fid-lues").text(parseInt(this.FIDs.length));
        $("#nb-lues").text(parseInt(this.FIDs.length + this.QCMs.length));
    }

    hasFID(code_barre) {
//vérifie si une fid a été traitée
        return this.FIDs.findIndex((e) => e.code_barre === code_barre) !== -1;
    }

    getFID(code_barre) {
        return this.FIDs[this.FIDs.findIndex((e) => e.code_barre === code_barre)];
    }

    removeFID(code_barre) {
//supprime une fid de la liste des fids traités
        this.FIDs = this.FIDs.splice(this.FIDs.findIndex((e) => e.code_barre === code_barre), 1);
        $("#nb-fid-lues").text(parseInt($("#nb-fid-lues").text()) - 1);
        this.gridOptions.api.setRowData(this.FIDs);
    }

    hasQCM(code_barre) {
//vérifie si un qcm a été traité
        return this.QCMs.findIndex((e) => e.code_barre === code_barre) !== -1;
    }

    getQCM(code_barre) {
        return this.QCMs[this.QCMs.findIndex((e) => e.code_barre === code_barre)];
    }

    storeQCM(qcm) {
//ajoute une fid à la liste des fid traités
        this.QCMs = this.QCMs.concat([qcm]);
        if (this.hasFID(qcm.code_barre)) {
            this.codesAppaires.push(qcm.code_barre);
            $("#nb-appaires").text(parseInt($("#nb-appaires").text()) + 2);
        }
        $("#nb-qcm-lues").text(parseInt(this.QCMs.length));
        $("#nb-lues").text(parseInt(this.FIDs.length + this.QCMs.length));
    }

    removeQCM(code_barre) {
//supprime une fid de la liste des fids traités
        this.QCMs = this.QCMs.splice(this.QCMs.findIndex((e) => e.code_barre === code_barre), 1);
        $("#nb-qcm-lues").text(parseInt($("#nb-qcm-lues").text()) - 1);
    }

    async correctFID(fid) {
        const forms = [];
        const blanck = '_';
        const unknown = '?';
        const not_asked = 'not_asked';
        const siecleCorrection = 20;
        const anneeCorrection = 23;
        for (var field in this.contentFID) {
            const ligne = this.contentFID[field];
            const requestField = this.request[field];
            if (requestField.asked) {
                //si le champs est demandé
                if (fid[field].match(ligne.regex)) {
                    //si la lecture est coérente
                    if (ligne.type === 'choice') {
                        for (let i in ligne.choice) {
                            if (ligne.choice[i].read === fid[field]) {
                                fid[field] = ligne.choice[i].store;
                            }
                        }
                    }
                    if (ligne.type === 'date') {
                        const read = fid[field];
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
                                }, function () {
                                }));
                        }
                    }
                } else {
                    //si la lecture est incohérente

                    if (requestField.expected !== false) {
                        forms.push(formConfirm(field, ligne.name, fid[field], requestField.expected,
                            function () {
                                fid[field] = my.request[field].expected;
                            }, function () {
                            }));
                    } else {

                        if (ligne.type === 'choice') {
                            forms.push(formSelect(field, ligne.name, ligne.choice, function (r) {
                                fid[field] = r;
                            }, function () {
                                fid[field] = blanck;
                            }, function () {
                                fid[field] = unknown;
                            }));
                        } else {
                            if (ligne.type === 'date') {
                                forms.push(formDate(field, ligne.name, fid[field], function (r) {
                                    fid[field] = r;
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
                    }
                }
            } else {
                //si le champs n'est pas demandé
                fid[field] = not_asked;
            }

        }
        //la lecture est terminée, on demande les corrections nécessaires
        if (forms.length > 0) {
            await this.port.write('S');
            var my = this;
            askFID(fid.code_barre, forms, function () {
                for (let i in forms) {
                    const line = forms[i];
                    field = line.field;
                    line.action();
                }
                my.storeFID(fid);
                my.readFIDs();
            }, function () {
                my.readFIDs();
            });
        } else {
            this.port.write('G');
            this.storeFID(fid);
            this.readFIDs();
        }


    }

    async correctQCM(code_barre, qcm) {
        const blanck = 0;
        const unknown = 0;
        const notasked = 0;
        const corresp = {'A': 1, 'B': 2, 'D': 3, 'H': 4, 'P': 5};
        const toCorrect = [];
        for (let i = 0; i < this.nbQuestions; i++) {
            if ((i + 1).toString() in this.questions) {
                if (['A', 'B', 'D', 'H', 'P'].includes(qcm[i])) {
                    qcm[i] = corresp[qcm[i]];
                } else {
                    if (qcm[i] === '@') {
                        if (questions[i] == "Score") {
                            toCorrect.push({numero: i, blanck: true, unknown: false});
                        } else {
                            qcm[i] = blanck;
                        }
                    } else {
                        toCorrect.push({numero: i, blanck: false, unknown: true});
                    }
                }
            } else {
                qcm[i] = notasked;
            }
        }
        if (toCorrect.length > 0) {
            await this.port.write('S');
            const my = this;
            askQCM(code_barre, toCorrect, function (rep) {
                for (let j in rep) {
                    qcm[rep[j].question] = rep[j].response;
                }
                my.storeQCM({code_barre: code_barre, reponses: qcm});
                my.readQCMs();
            }, function () {
                my.readQCMs();
            }, blanck, unknown);
        } else {
            await this.port.write('G');
            this.storeQCM({code_barre: code_barre, reponses: qcm});
            this.readQCMs();
        }

    }

    readFID(text) {
        const fid = {};
        let cursor = 0;
        for (let field in this.contentFID) {
            const ligne = this.contentFID[field];
            const step = ligne.length;
            fid[field] = text.slice(cursor, cursor + step);
            cursor += step;
        }
        if (this.hasFID(fid.code_barre)) {
            this.port.write('G');
            this.readFIDs();
        } else {
            this.correctFID(fid);
        }

    }

    readQCM(text) {
        const qcm = [];
        for (let i = 0; i < this.nbQuestions; i++) {
            qcm[i] = text[8 + i];
        }
        if (this.hasQCM(text.slice(0, 8))) {
            this.port.write('G');
            this.readQCMs();
        } else {
            this.correctQCM(text.slice(0, 8), qcm);
        }

    }

    static codesErreurs = [
        {sequence: "Erreur marques de cadrage", message: "Marques de cadrage"},
        {sequence: "Erreur nombre marques horloges", message: "Nombre marques horloges"},
    ];

    async readFIDs() {
        $("#spinner-fid").show();
        await timeout(10);
        const rep = await this.port.exchange('L');
        const bac_vide = "\x1506\r\n\x03";
        if (rep.includes(bac_vide)) {
            //on n'a plus de page à lire
            //on revoie une commande L pour baisser le bac
            await this.port.write('L')
            //fin de lecture
            $("#spinner-fid").hide();
            return true;
        }

        //si il y a effectivement une page à lire
        for (let i in GrilleManager.codesErreurs) {
            const erreur = GrilleManager.codesErreurs[i];
            var my = this;
            if (rep.includes(erreur.sequence)) {
                await this.port.write('S');
                return tellFatalError(erreur.message, "Lire la page suivante", async function () {
                    return my.readFIDs();
                });
            }
        }
        var regex = /\x01\x02[0-9]{8}(.{83})[0-9]{3}\r\n\x03\x04/;
        var match = rep.match(regex);
        if (match === null) {
            //on regarde si ce n'est pas 'juste' un problème de code barre
            var regex = /\x01\x02.{8}.{8}(.{75})[0-9]{3}\r\n\x03\x04/;
            var match = rep.match(regex);
            if (match !== null) {
                const suite = match[1];
                const propal = "1000000" + this.noCodeBarreFID.toString();
                var my = this;
                await this.port.write('S');
                askCodeBarre(propal, function (r) {
                    return my.readFID(r + suite);
                }, function () {
                    my.noCodeBarreFID = my.noCodeBarreFID + 1;
                    return my.readFID(propal + suite);
                }, async function () {
                    my.readFIDs();
                });
            } else {
                var my = this;
                await this.port.write('S');
                return tellFatalError("Réponse reçue : " + rep, "Lire la page suivante", async function () {
                    my.readFIDs();
                });
            }

        } else {
            const expl = match[1];
            return this.readFID(expl);
        }
    }

    async readQCMs() {
        $("#spinner-qcm").show();
        await timeout(10);
        const rep = await this.port.exchange('L');

        const bac_vide = "\x1506\r\n\x03";

        if (rep.includes(bac_vide)) {
            //on n'a plus de page à lire
            //on revoie une commande L pour baisser le bac
            await this.port.write('L')
            //fin de lecture
            $("#spinner-qcm").hide();
            return true;
        }

        //si il y a effectivement une page à lire
        for (let i in GrilleManager.codesErreurs) {
            const erreur = GrilleManager.codesErreurs[i];
            var my = this;
            if (rep.includes(erreur.sequence)) {
                await this.port.write('S');
                return tellFatalError(erreur.message, "Lire la page suivante", async function () {
                    return my.readQCMs();
                });
            }
        }
        var regex = /\x01\x02[0-9]{8}(.{648})[0-9]{3}\r\n\x03\x04/;
        var match = rep.match(regex);
        if (match === null) {
            //on regarde si ce n'est pas 'juste' un problème de code barre
            var regex = /\x01\x02.{8}.{8}(.{640})[0-9]{3}\r\n\x03\x04/;
            var match = rep.match(regex);
            if (match !== null) {
                const suite = match[1];
                const propal = "2000000" + this.noCodeBarreQCM.toString();
                var my = this;
                await this.port.write('S');
                askCodeBarre(propal, function (r) {
                    return my.readQCM(r + suite);
                }, function () {
                    my.noCodeBarreQCM = my.noCodeBarreQCM + 1;
                    return my.readQCM(propal + suite);
                }, async function () {
                    my.readQCMs();
                });
            } else {
                var my = this;
                await this.port.write('S');
                return tellFatalError("Réponse reçue : " + rep, "Lire la page suivante", async function () {
                    my.readQCMs();
                });
            }
        } else {
            const expl = match[1];
            this.readQCM(expl);
        }

    }

    manualLink() {
        const nbPagesAAppairer = this.FIDs.length + this.QCMs.length - 2 * this.codesAppaires.length;
        if (nbPagesAAppairer % 2 === 1) {
            tellFatalError("Le nombre de pages à appairer n'est pas paire, veuillez poursuivre la correction", "Continuer", function () {
            });
        } else {
            if (nbPagesAAppairer === 0) {
                tellFatalError("Aucune page à appairer manuellement.", "Continuer", function () {
                });
            } else {
                const FIDsDispo = [];
                const QCMsDispo = [];
                for (var i in this.FIDs) {
                    if (!this.codesAppaires.includes(this.FIDs[i].code_barre)) {
                        FIDsDispo.push({code_barre: this.FIDs[i].code_barre, nom: this.FIDs[i].nom});
                    }
                }
                for (var i in this.QCMs) {
                    if (!this.codesAppaires.includes(this.QCMs[i].code_barre)) {
                        QCMsDispo.push({code_barre: this.QCMs[i].code_barre});
                    }
                }
                const my = this;
                askManualLink(nbPagesAAppairer, FIDsDispo, QCMsDispo, function (rep) {
                    for (let i in rep) {
                        if (!(my.codesAppaires.includes(rep[i].fid) || my.codesAppaires.includes(rep[i].qcm))) {
                            my.getQCM(rep[i].qcm).code_barre = rep[i].fid;
                            my.codesAppaires.push(rep[i].fid);
                            $("#nb-appaires").text(parseInt($("#nb-appaires").text()) + 2);
                        }
                    }
                }, function () {
                });
            }

        }

    }

    save(session) {
        const rep = {};
        for (let i in this.codesAppaires) {
            const code = this.codesAppaires[i];
            rep[code] = this.FIDs[this.FIDs.findIndex((e) => e.code_barre === code)];
            rep[code].qcm = this.QCMs[this.QCMs.findIndex((e) => e.code_barre === code)].reponses;
        }
        $.post('/lecture/scanner/save', {data: JSON.stringify(rep), session: session}, function (rep) {
            $("#manual-end").modal("show");
        });
    }
}
