/* 
 * Page qui gère les interventions utilisteur pour la correction manuelle de certaines choses
 */

function tellFatalError(message, button, next) {
    $('#manual-fatal .message').text(message);
    $('#manual-fatal .button').text(button);
    $("#manual-fatal").modal("show");
    $("#manual-fatal .ignorer").off();
    $("#manual-fatal .ignorer").click(function () {
        next();
    });
}



/*ouvre la modale de correction manuelle d'une fid
 * 
 * code_barre
 * forms -> [{html: 'code du formulaire', action: callback},....]
 * valider -> callback si click sur valider
 * annuler -> callback si clic sur annuler
 */
function askFID(code_barre, forms, valider, annuler) {
    $('#manual-FID form').empty();
    $('#manual-FID .code-barre').text(code_barre);
    for (var i in forms) {
        var line = forms[i];
        $('#manual-FID form').append("<div class='row mt-4 mb-4 ligne-" + line.field + "'>" + line.html + "</div>");
    }
    $("#manual-FID").modal("show");
    $("#manual-FID .valider").off();
    $("#manual-FID .annuler").off();
    //vlaidation du formulaire
    $("#manual-FID .valider").click(function () {
        valider();
    });
    $("#manual-FID .annuler").click(function () {
        annuler();
    });
}


/*renvoie un {form} de type input
 * 
 * field -> champs modifié
 * field_name -> affichage du nom du champs
 * value -> valeur actuelle du champs
 * type -> text, date, number ...
 * valid -> callback si validé
 * left_void -> callback si l'utilisateur coche 'case laissée vide par le candidat'
 * unknown -> callback si l'utilisateur coche 'impssible de dire c'est quelle case'
 */
function formInput(field, field_name, value, type, valid, left_void, unknown) {
    var html = "<div class='col-3'><span>" + field_name + "</span></div>\n\
<div class='col-4'><input type='" + type + "' class='form-control res' value='" + value + "'></div>\n\
<div class='col-2 offset-1'><div class='form-check'>\n\
  <input class='form-check-input vide' type='checkbox'>\n\
  <label class='form-check-label'>\n\
    Laissé vide\n\
  </label>\n\
</div></div>\n\
<div class='col-2'><div class='form-check'>\n\
  <input class='form-check-input flou' type='checkbox'>\n\
  <label class='form-check-label'>\n\
    Indiscernable\n\
  </label>\n\
</div></div>";
    return {
        html: html,
        field: field,
        action: function () {
            if ($(".ligne-" + field + " .vide").prop('checked')) {
                return left_void();
            }
            if ($(".ligne-" + field + " .flou").prop('checked')) {
                return unknown();
            }
            var r = $(".ligne-" + field + " .res").val();
            valid(r);
        }
    };
}


/*renvoie un {form} pour un cas ou on demande à l'utilisteur de corriger ou non un champs
 * par la valeur attendu (pour le numéro de batterie par exemple)
 * 
 * field -> champs modifié
 * field_name -> affichage du nom du champs
 * value -> valeur actuelle du champs
 * expected -> valuer attendue pour cette session
 * correct -> callback si l'utilisateur veut corriger l'erreur
 * ignore -> callback si l'utilisateur veut laisser le champs tel quel
 */
function formConfirm(field, field_name, value, expected, correct, ignore) {
    var html = "<div class='col-3'><span>" + field_name + "</span></div>\n\
<div class='col-4'><span>Reseigné : <strong>" + value + "</strong>  /   Attendu : <strong>" + expected + "</strong></span></div>\n\
<div class='col-2 offset-1'><div class='form-check'>\n\
  <input class='form-check-input correct' type='radio' name='radio-" + field + "' checked>\n\
  <label class='form-check-label'>\n\
    Corriger\n\
  </label>\n\
</div></div>\n\
<div class='col-2'><div class='form-check'>\n\
  <input class='form-check-input ignore' type='radio' name='radio-" + field + "' >\n\
  <label class='form-check-label'>\n\
    Laisser\n\
  </label>\n\
</div></div>";
    return {
        html: html,
        field: field,
        action: function () {
            if ($(".ligne-" + field + " .correct").attr('checked') === 'checked') {
                return correct();
            } else {
                return ignore();
            }

        }
    };
}


/*renvoie un {form} de type select
 * 
 * field -> champs modifié
 * field_name -> affichage du nom du champs
 * choice -> représente les différentes options  [{read: 'ce qui est lu par le lecteur', print:'ce qui s'affiche dans les choix', store:'valeur finale'},...]
 * valid -> callback si validé
 * left_void -> callback si l'utilisateur coche 'case laissée vide par le candidat'
 * unknown -> callback si l'utilisateur coche 'impssible de dire c'est quelle case'
 */
function formSelect(field, field_name, choice, valid, left_void, unknown) {
    var html = "<div class='col-3'><span>" + field_name + "</span></div>\n\
<div class='col-4'><select class='form-select'>";
    for (var key in choice) {
        var line = choice[key];
        html += "<option value='" + line.store + "'>" + line.print + "</option>";
    }
    html += "</select></div>\n\
<div class='col-2 offset-1'><div class='form-check'>\n\
  <input class='form-check-input' type='checkbox' class='vide'>\n\
  <label class='form-check-label'>\n\
    Laissé vide\n\
  </label>\n\
</div></div>\n\
<div class='col-2'><div class='form-check'>\n\
  <input class='form-check-input' type='checkbox' class='flou'>\n\
  <label class='form-check-label'>\n\
    Indiscernable\n\
  </label>\n\
</div></div>";
    return {
        html: html,
        field: field,
        action: function () {
            if ($(".ligne-" + field + " .vide").prop('checked')) {
                return left_void();
            }
            if ($(".ligne-" + field + " .flou").prop('checked')) {
                return unknown();
            }
            var r = $(".ligne-" + field + " select").val();
            return valid(r);
        }
    };
}


//si la FID a déjà été lue, on demande si relecture
function askAlready(code_barre, valider, annuler) {
    $('#manual-already .code').text(code_barre);
    $("#manual-already").modal("show");
    $("#manual-already .valider").off();
    $("#manual-already .annuler").off();
    //vlaidation du formulaire
    $("#manual-already .valider").click(function () {
        valider();
    });
    $("#manual-already .annuler").click(function () {
        annuler();
    });
}


//ligne de form de QCM
function makeHTMLQCM(question, blanck, unknown, initial_blank, inital_unknown) {
    return "<div class='row mt-3 mb-3 qcm-" + question + "'>\n\
<div class='col-2'>\n\
<strong>" + (question + 1).toString() + "</strong>\n\
</div>\n\
                            <div class='form-check col-1'>\n\
                                <input class='form-check-input' type='radio' value='A' name='ligne-" + question + "'>\n\
                                <label class='form-check-label'>A</label>\n\
                            </div>\n\
                            <div class='form-check col-1'>\n\
                                <input class='form-check-input' type='radio' value='B' name='ligne-" + question + "'>\n\
                                <label class='form-check-label'>B</label>\n\
                            </div>\n\
                            <div class='form-check col-1'>\n\
                                <input class='form-check-input' type='radio' value='C' name='ligne-" + question + "'>\n\
                                <label class='form-check-label'>C</label>\n\
                            </div>\n\
                            <div class='form-check col-1'>\n\
                                <input class='form-check-input' type='radio' value='D' name='ligne-" + question + "'>\n\
                                <label class='form-check-label'>D</label>\n\
                            </div>\n\
                            <div class='form-check col-1'>\n\
                                <input class='form-check-input' type='radio' value='E' name='ligne-" + question + "'>\n\
                                <label class='form-check-label'>E</label>\n\
                            </div>\n\
                            <div class='col-2 offset-1'>\n\
                                <input class='form-check-input' type='radio' value='" + blanck + "' name='ligne-" + question + "' " + (initial_blank ? "checked" : "") + ">\n\
                                <label class='form-check-label'>Vide</label>\n\
                            </div>\n\
                            <div class='col-2'>\n\
                                <input class='form-check-input' type='radio' value='" + unknown + "' name='ligne-" + question + "' " + (inital_unknown ? "checked" : "") + ">\n\
                                <label class='form-check-label'>Indiscernable</label>\n\
                            </div>\n\
                        </div>";
}

function askQCM(code_barre, questions, valider, annuler, blanck, unknown) {
    $('#manual-QCM form').empty();
    $('#manual-QCM .code-barre').text(code_barre);
    for (var i in questions) {
        var question = questions[i];
        $('#manual-QCM form').append(makeHTMLQCM(question.numero, blanck, unknown, question.blanck, question.unknown));
    }
    $("#manual-QCM").modal("show");
    $("#manual-QCM .valider").off();
    $("#manual-QCM .annuler").off();
    //validation du formulaire
    $("#manual-QCM .valider").click(function () {
        var rep = [];
        for (var i in questions) {
            var question = questions[i];
            var r = $('#manual-QCM .qcm-' + question + ' input:checked').val();
            rep.push({question: question, response: r});
        }
        valider(rep);
    });
    $("#manual-QCM .annuler").click(function () {
        annuler();
    });
}

//si la FID a déjà été lue, on demande si relecture
function askCodeBarre(propal, valider, manual, ignorer) {
    $('#manual-code-barre .scanette').val("");
    $('#manual-code-barre .propal').text(propal);
    $("#manual-code-barre").modal("show");
    $("#manual-code-barre .valider").off();
    $("#manual-code-barre .ignorer").off();
    //vlaidation du formulaire
    $("#manual-code-barre .valider").click(function () {
        var rep = $("#manual-code-barre .scanette").val();
        return valider(rep);
    });
    $("#manual-code-barre .ignorer").click(function () {
        return ignorer();
    });
    $("#manual-code-barre .manuel").click(function () {
        return manual();
    });
}

function makeHTMLLink(FIDs, QCMs, i) {
    var r = "<div class='row mb-3'><div class='col-6'><select class='form-select fid-" + i + "'>";
    for (var j in FIDs) {
        r += "<option value='" + FIDs[j].code_barre + "'>" + FIDs[j].code_barre + " - " + FIDs[j].nom + "</option>";
    }
    r += "</select></div><div class='col-6'><select class='form-select qcm-" + i + "'>";
    for (var j in QCMs) {
        r += "<option value='" + QCMs[j].code_barre + "'>" + QCMs[j].code_barre + "</option>";
    }
    r += " </select></div></div>";
    return r;
}


function askManualLink(nb, FIDs, QCMs, valider, annuler) {
    $('#manual-link .nb-a-appaires').text(nb);
    $('#manual-link form').empty();
    for (var i = 0; i < (nb / 2); i++) {
        $('#manual-link form').append(makeHTMLLink(FIDs, QCMs, i));
    }
    $("#manual-link").modal("show");
    $("#manual-link .valider").off();
    $("#manual-link .annuler").off();
    $("#manual-link .valider").click(function () {
        var rep = [];
        for (var i = 0; i < (nb / 2); i++) {
            rep[i] = {fid: $("#manual-link .fid-"+i).val(), qcm: $("#manual-link .qcm-"+i).val()};
        }
        return valider(rep);
    });
    $("#manual-link .annuler").click(function () {
        return annuler();
    });
}
