# Cortest

*Correction de tests QCM.*

## Présentation

Cortest est un projet de correction de test de type QCM, pour les examens psycotechniques de la police nationale. Les
principales fonctionnalités sont les suivantes :

* Interfaçage avec un lecteur optique pour traiter automatiquement les réponses des candidats.
* Création/modification/suppression de **profils**, composés d'**échelles**, elles-mêmes calculées à partir des réponses
  des candidats.
* Création/modification/suppression d'**étalonnages**, permettant de convertir des notes en profils.
* Génération de **feuilles de profil**, au format *.pdf*, décrivant le profil d'un candidat et ses informations
  personnelles

## Description technique

* Le dossier [assets](assets) contient les différents modules *.js* et *.css* du projet.
* Le dossier [bin](bin) contient les exécutables pour développer le projet.
* Le dossier [config](config) contient les fichiers de configuration du projet
* Le dossier [doc](doc) contient la documentation recueillie au long du travail sur le projet et susceptible d'être
  nécessaire au développement.
* Le dossier [migrations](migrations) contient les migrations générées automatiquement par **symfony**.
* Le dossier [src](src) contient les fichiers source *.php* du projet.
* Le dossier [templates](templates) contient les modèles graphiques, au format *.twig*.
* Le dossier [tests](tests) contient les tests unitaires, d'intégration et d'application du projet.
* Les dossiers [var](var) et [vendor](vendor) contiennent les dépendances locales, les fichiers de log et de cache
* Le fichier [.env](.env) contient les variables d'environnement communes aux différents environnements symfony (**dev
  **, *
  *test** et **prod**), et indépendants de la plateforme d'exécution.