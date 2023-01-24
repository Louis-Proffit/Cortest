/* 
 * Page qui gère les interventions utilisteur pour la correction manuelle de certaines choses
 */



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
    for (var field in forms) {
        var line = forms[field];
        $('#manual-FID form').append("<div class='row mt-4 mb-4 ligne-" + field + "'>" + line.html + "</div>");
    }
    $("#manual-FID").modal("show");
    $("#manual-FID .valider").off();
    $("#manual-FID .annuler").off();
    //vlaidation du formulaire
    $("#manual-FID .valider").click(function () {
        for (var field in forms) {
            var line = forms[field];
            line.action();
        }
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
<div class='col-4'><input type='" + type + "' class='form-control' value='" + value + "'></div>\n\
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
        action: function () {
            if ($(".ligne" + field + " .vide").attr('checked') === 'checked') {
                return left_void();
            }
            if ($(".ligne" + field + " .flou").attr('checked') === 'checked') {
                return unknown();
            }
            return valid($(".ligne" + field + " input").val());

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
  <input class='form-check-input' type='radio' name='radio-" + field + "' class='correct' checked>\n\
  <label class='form-check-label'>\n\
    Corriger\n\
  </label>\n\
</div></div>\n\
<div class='col-2'><div class='form-check'>\n\
  <input class='form-check-input' type='radio' name='radio-" + field + "' class='ignore'>\n\
  <label class='form-check-label'>\n\
    Laisser\n\
  </label>\n\
</div></div>";
    return {
        html: html,
        action: function () {
            if ($(".ligne" + field + " .correct").attr('checked') === 'checked') {
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
        action: function () {
            if ($(".ligne" + field + " .vide").attr('checked') === 'checked') {
                return left_void();
            }
            if ($(".ligne" + field + " .flou").attr('checked') === 'checked') {
                return unknown();
            }
            return valid($(".ligne" + field + " select").val());

        }
    };
}