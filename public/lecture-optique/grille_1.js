/* 
 * Lecture de la grille du cahier des charges
 */

class GrilleManager {
    
    //représente une feuille FID et comment elle doit être lue
    static contentFID = {
        code_barre: {name: 'Code barre', type: 'int', length: 8, regex: /^[0-9]{8}$/},
        nom: {name: 'Nom', type: 'string', length: 15, regex: /^[A-Z]*[\s]*}$/},
        prenom: {name: 'Prénom', type: 'string', length: 11, regex: /^[A-Z]*[\s]*}$/},
        nom_jeune_fille: {name: 'Nom de jeune fille', type: 'string', length: 13, regex: /^[A-Z]*[\s]*}$/},
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
        sexe: {name: 'sexe', type: 'choice', lenght: 1, regex: /^[1-2]$/, choice: [
                {read: '1', print: 'M', store: 'M'},
                {read: '2', print: 'F', store: 'F'},
            ]},
        concours: {name: 'Concours', type: 'choice', lenght: 1, regex: /^[EIRS]$/, choice: [
                {read: 'E', print: 'E', store: 'E'},
                {read: 'I', print: 'I', store: 'I'},
                {read: 'R', print: 'R', store: 'R'},
                {read: 'S', print: 'S', store: 'S'},
            ]},
        SGAP: {name: 'SGAP', type: 'int', length: 2, regex: /^[0-9]{2}$/},
        date_examen: {name: 'Date d\'examen', type: 'date', length: 6, regex: /[0-3][0-9][0-1][0-9]{3}/},
        type_concours: {name: 'Type concours', type: 'int', length: 2, regex: /^[0-9]{2}$/},
        batterie: {name: 'Batterie', type: 'int', length: 3, regex: /^[0-9]{3}$/},
        reserve: {name: 'Reservé', type: 'int', length: 5, regex: /^[0-9]{5}$/},
        option_1: {name: 'Option 1', type: 'int', length: 4, regex: /^[0-9]{4}$/},
        option_2: {name: 'Option 2', type: 'int', length: 6, regex: /^[0-9]{6}$/},
    };

    constructor(request) {
        
        /* request décrit ce qui est attendu pour cette session
         * elle doit être de la forme
         * 
         * request = {
            code_barre: {asked: true, value: false, print: true},
            nom: {asked: true, value: false, print: true},
            ...
            }
         */
        
        this.request = request;
        this.FIDs = [];
        this.QCMs = [];
    }

    getGridConfig() {
        //renvoie la configuration pour le tableau AG-GRID
        var columnDefs = [];

        for (var field in GrilleManager.contentFID) {
            var ligne = GrilleManager.contentFID[field];
            if (this.request[name].print) {
                switch (ligne.type) {
                    case 'date':
                        columnDefs.push({field: field, headerName: ligne.name, type: 'dateColumn'});
                        break;
                    case 'choice':
                        columnDefs.push({field: field, headerName: ligne.name, cellEditor: 'agSelectCellEditor', cellEditorParams: {values: field.choice}});
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

    store(fid) {
        //ajoute une fid à la liste des fid traités
        this.FIDs = this.FIDs.concat([fid])
        this.gridOptions.api.setRowData(FIDs);
    }

    hasFID(code_barre) {
        //vérifie si une fid a été traitée
        return this.FIDs.findIndex((e) => e.code_barre === code_barre) !== -1;
    }

    hasQCMs(code_barre) {
        //vérifie si un qcm a été traité
        return this.QCMs.findIndex((e) => e.code_barre === code_barre) !== -1;
    }

    removeFID(code_barre) {
        //supprime une fid de la liste des fids traités
        this.FIDs = this.FIDs.splice(this.FIDs.findIndex((e) => e.code_barre === code_barre), 1);
    }
    
    
    correct(fid) {
        
    }
}


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


//on définit comment interpréter chaque champs

const reader = {
    code_barre: function (read) {
        if (read.match(/[0-9]{8}/)) {
            if (hasFID(read)) {
                return [read, false, function (running_fid) {
                        askUserAlreadyRead(running_fid);
                    }];
            }
            return [read, true, null];
        } else {
            return [read, false, function (running_fid) {
                    askUserText(running_fid, 'code_barre', "Le code barre n'est pas valide", "?");
                }];
        }
    },
    nom: function (read) {
        if (read.match(/[?]/)) {
            return [read, false, function (running_fid) {
                    askUserText(running_fid, 'nom', "Une colonne est cochée deux fois ou plus", "?");
                }];
        } else {
            if (read.match(/[A-Z]\s[A-Z]/)) {
                return [read, false, function (running_fid) {
                        askUserText(running_fid, 'nom', "Il y a un espace dans la réponse", "?");
                    }];
            } else {
                return [read, true, null];
            }

        }
    },
    prenom: function (read) {
        if (read.match(/[?]/)) {
            return [read, false, function (running_fid) {
                    askUserText(running_fid, 'prenom', "Une colonne est cochée deux fois ou plus", "?");
                }];
        } else {
            if (read.match(/[A-Z]\s[A-Z]/)) {
                return [read, false, function (running_fid) {
                        askUserText(running_fid, 'prenom', "Il y a un espace dans la réponse", "?");
                    }];
            } else {
                return [read, true, null];
            }

        }
    },
    nom_jeune_fille: function (read) {
        if (read.match(/[?]/)) {
            return [read, false, function (running_fid) {
                    askUserText(running_fid, 'nom_jeune_fille', "Une colonne est cochée deux fois ou plus", "?");
                }];
        } else {
            if (read.match(/[A-Z]\s[A-Z]/)) {
                return [read, false, function (running_fid) {
                        askUserText(running_fid, 'nom_jeune_fille', "Il y a un espace dans la réponse", "?");
                    }];
            } else {
                if (read.match(/\s{13}/)) {
                    return ['non renseigné', true, null];
                } else {
                    return [read, true, null];
                }
            }

        }
    },
    niveau_scolaire: function (read) {
        if (read.match(/[?]/)) {
            return [read, false, function (running_fid) {
                    askUserChoice(running_fid, 'niveau_scolaire', "Une colonne est cochée deux fois ou plus", sheet_fid_content.niveau_scolaire.choice, "?");
                }];
        } else {
            if (read.match(/[1-8]/)) {
                return [sheet_fid_content.niveau_scolaire.choice[parseInt(read) - 1], true, null];
            } else {
                return [read, false, function (running_fid) {
                        askUserChoice(running_fid, 'niveau_scolaire', "Réponse absente ou invalide", sheet_fid_content.niveau_scolaire.choice, "?");
                    }];
            }

        }
    },
    date_naissance: function (read) {
        if (read.match(/[?]/)) {
            return [read, false, function (running_fid) {
                    askUserText(running_fid, 'date_naissance', "Une colonne est cochée deux fois ou plus", "?", true);
                }];
        } else {
            if (read.match(/[0-3][0-9][0-1][0-9]{3}/)) {
                return [read[0] + read[1] + '/' + read[2] + read[3] + '/' + read[4] + read[5] + read[6] + read[7], true, null];
            } else {
                return [read, false, function (running_fid) {
                        askUserText(running_fid, 'date_naissance', "Réponse absente ou invalide", "?", true);
                    }];
            }

        }
    },
    sexe: function (read) {
        if (read.match(/[?]/)) {
            return [read, false, function (running_fid) {
                    askUserChoice(running_fid, 'sexe', "Une colonne est cochée deux fois ou plus", sheet_fid_content.sexe.choice, "?");
                }];
        } else {
            if (read.match(/[1-2]/)) {
                return [sheet_fid_content.sexe.choice[parseInt(read) - 1], true, null];
            } else {
                return [read, false, function (running_fid) {
                        askUserChoice(running_fid, 'sexe', "Réponse absente ou invalide", sheet_fid_content.sexe.choice, "?");
                    }];
            }

        }
    },
    concours: function (read) {
        if (read.match(/[?]/)) {
            return [read, false, function (running_fid) {
                    askUserChoice(running_fid, 'concours', "Une colonne est cochée deux fois ou plus", sheet_fid_content.concours.choice, "?");
                }];
        } else {
            if (read.match(/[EIRS]/)) {
                return [read, true, null];
            } else {
                return [read, false, function (running_fid) {
                        askUserChoice(running_fid, 'concours', "Réponse absente ou invalide", sheet_fid_content.concours.choice, "?");
                    }];
            }

        }
    },
    SGAP: function (read) {
        if (read.match(/[?]/)) {
            return [read, false, function (running_fid) {
                    askUserText(running_fid, 'SGAP', "Une colonne est cochée deux fois ou plus", "?");
                }];
        } else {
            if (read.match(/[0-9]{2}/)) {
                return [read, true, null];
            } else {
                return [read, false, function (running_fid) {
                        askUserText(running_fid, 'SGAP', "Le nombre est absent ou incorrect", "?");
                    }];
            }
        }
    },
    date_examen: function (read) {
        if (read.match(/[?]/)) {
            return [read, false, function (running_fid) {
                    askUserText(running_fid, 'date_examen', "Une colonne est cochée deux fois ou plus", "?", true);
                }];
        } else {
            if (read.match(/[0-3][0-9][0-1][0-9]{3}/)) {
                return [read[4] + read[5] + read[6] + read[7] + '-' + read[2] + read[3] + '-' + read[0] + read[1], true, null];
            } else {
                return [read, false, function (running_fid) {
                        askUserText(running_fid, 'date_examen', "Réponse absente ou invalide", "?", true);
                    }];
            }

        }
    },
    type_concours: function (read) {
        if (read.match(/[?]/)) {
            return [read, false, function (running_fid) {
                    askUserText(running_fid, 'type_concours', "Une colonne est cochée deux fois ou plus", "?");
                }];
        } else {
            if (read.match(/[0-9]{2}/)) {
                return [read, true, null];
            } else {
                return [read, false, function (running_fid) {
                        askUserText(running_fid, 'type_concours', "Le nombre est absent ou incorrect", "?");
                    }];
            }
        }
    },
    batterie: function (read) {
        if (read.match(/[?]/)) {
            return [read, false, function (running_fid) {
                    askUserText(running_fid, 'batterie', "Une colonne est cochée deux fois ou plus", "?");
                }];
        } else {
            if (read.match(/[0-9]{3}/)) {
                return [read, true, null];
            } else {
                return [read, false, function (running_fid) {
                        askUserText(running_fid, 'batterie', "Le nombre est absent ou incorrect", "?");
                    }];
            }
        }
    },
    reserve: function (read) {
        if (read.match(/[?]/)) {
            return [read, false, function (running_fid) {
                    askUserText(running_fid, 'reserve', "Une colonne est cochée deux fois ou plus", "?");
                }];
        } else {
            if (read.match(/[0-9]{5}/)) {
                return [read, true, null];
            } else {
                return [read, false, function (running_fid) {
                        askUserText(running_fid, 'reserve', "Le nombre est absent ou incorrect", "?");
                    }];
            }
        }
    },
    option_1: function (read) {
        if (read.match(/[?]/)) {
            return [read, false, function (running_fid) {
                    askUserText(running_fid, 'option_1', "Une colonne est cochée deux fois ou plus", "?");
                }];
        } else {
            if (read.match(/[0-9]{4}/)) {
                return [read, true, null];
            } else {
                return [read, false, function (running_fid) {
                        askUserText(running_fid, 'option_1', "Le nombre est absent ou incorrect", "?");
                    }];
            }
        }
    },
    option_2: function (read) {
        if (read.match(/[?]/)) {
            return [read, false, function (running_fid) {
                    askUserText(running_fid, 'option_2', "Une colonne est cochée deux fois ou plus", "?");
                }];
        } else {
            if (read.match(/[0-9]{6}/)) {
                return [read, true, null];
            } else {
                return [read, false, function (running_fid) {
                        askUserText(running_fid, 'option_2', "Le nombre est absent ou incorrect", "?");
                    }];
            }
        }
    }
};

