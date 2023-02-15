<?php

namespace App\Fixture;

use App\Core\Renderer\Renderer;
use App\Core\Renderer\RendererRepository;
use App\Entity\Concours;
use App\Entity\Correcteur;
use App\Entity\CortestUser;
use App\Entity\Echelle;
use App\Entity\EchelleCorrecteur;
use App\Entity\EchelleEtalonnage;
use App\Entity\Etalonnage;
use App\Entity\Graphique;
use App\Entity\NiveauScolaire;
use App\Entity\Profil;
use App\Entity\QuestionConcours;
use App\Entity\ReponseCandidat;
use App\Entity\Session;
use App\Entity\Sgap;
use App\Repository\GrilleRepository;
use DateTime;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class InitFixture extends Fixture
{

    public function __construct(
        private readonly UserPasswordHasherInterface $password_hasher,
        private readonly GrilleRepository            $grille_repository,
        private readonly RendererRepository          $renderer_repository
    )
    {
    }

    public
    function load(ObjectManager $manager)
    {
        $sgaps = $this->sgaps();
        foreach ($sgaps as $sgap) {
            $manager->persist($sgap);
        }

        $echelles = $this->echelles();
        foreach ($echelles as $echelle) {
            $manager->persist($echelle);
        }

        $all_concours = $this->concours();
        foreach ($all_concours as $concours) {
            $manager->persist($concours);
        }

        $niveaux_scolaire = $this->niveau_scolaire();
        foreach ($niveaux_scolaire as $niveau_scolaire) {
            $manager->persist($niveau_scolaire);
        }

        $profil = new Profil(id: 0,
            nom: "Profil cahier des charges",
            echelles: new ArrayCollection($echelles),
            etalonnages: new ArrayCollection(),
            graphiques: new ArrayCollection());

        $manager->persist($profil);

        $correcteur = $this->correcteur($profil, $all_concours[0]);
        $manager->persist($correcteur);

        $etalonnage = $this->etalonnage($profil, 9);
        $manager->persist($etalonnage);

        foreach ($this->users() as $user) {
            $manager->persist($user);
        }

        $session = $this->session($all_concours[0], $sgaps[0]);
        $manager->persist($session);

        $reponseCandidat = $this->reponseCandidat($session, $niveaux_scolaire[0]);
        $manager->persist($reponseCandidat);

        $graphique = $this->graphique($profil, RendererRepository::INDEX_BATONNETS);
        $manager->persist($graphique);

        $manager->flush();
    }

    public
    function reponseCandidat(Session $session, NiveauScolaire $niveau_scolaire): ReponseCandidat
    {
        return new ReponseCandidat(
            id: 0,
            session: $session,
            reponses: array(0, 1, 2, 3, 4, 5, 3, 1, 2, 3, 5, 4, 0, 1, 2, 3, 5, 4, 1, 2, 0, 2, 3, 4),
            // TODO adapt to grille
            nom: "Nom d'exemple",
            prenom: "Prénom d'exemple",
            nom_jeune_fille: "Nom d'exemple",
            niveau_scolaire: $niveau_scolaire,
            date_de_naissance: new DateTime("now"),
            sexe: ReponseCandidat::INDEX_HOMME,
            reserve: "",
            autre_1: "",
            autre_2: "",
            code_barre: 0,
            raw: null);
    }

    public
    function session(Concours $concours, Sgap $sgap): Session
    {
        return new Session(
            id: 0,
            date: new DateTime("now"),
            numero_ordre: 0,
            observations: "Session d'exemple",
            concours: $concours,
            sgap: $sgap,
            reponses_candidats: new ArrayCollection()
        );
    }

    private
    function etalonnage(Profil $profil, int $num_echelles): Etalonnage
    {

        $etalonnage = new Etalonnage(
            id: 0,
            profil: $profil,
            nom: "Etalonnage d'exemple",
            nombre_classes: $num_echelles,
            echelles: new ArrayCollection()
        );

        /** @var Echelle $echelle */
        foreach ($profil->echelles as $echelle) {
            $bounds = [];

            for ($bound = 0; $bound < $num_echelles; $bound++) {
                $bounds[$bound] = $bound;
            }
            $etalonnage->echelles->add(
                new EchelleEtalonnage(
                    id: 0, bounds: $bounds, echelle: $echelle, etalonnage: $etalonnage
                )
            );
        }

        return $etalonnage;
    }

    private
    function correcteur(Profil $profil, Concours $concours): Correcteur
    {
        $correcteur = new Correcteur(
            id: 0,
            concours: $concours,
            profil: $profil,
            nom: "Correcteur exemple",
            echelles: new ArrayCollection()
        );

        /** @var Echelle $echelle */
        foreach ($profil->echelles as $echelle) {
            $correcteur->echelles->add(
                new EchelleCorrecteur(
                    id: 0, expression: "0", echelle: $echelle, correcteur: $correcteur
                )
            );
        }

        return $correcteur;
    }

    private
    function users(): array
    {

        $admin = new CortestUser(
            id: 0,
            username: "admin",
            password: "svUTr7FL87zm64",
            role: CortestUser::ROLE_ADMINISTRATEUR
        );

        $psycologue = new CortestUser(
            id: 0,
            username: "psycologue",
            password: "UvsTr8L7Fz76m4",
            role: CortestUser::ROLE_PSYCOLOGUE
        );

        $correcteur = new CortestUser(
            id: 0,
            username: "correcteur",
            password: "rvsUTF8L776zm4",
            role: CortestUser::ROLE_CORRECTEUR
        );

        $admin->password = $this->password_hasher->hashPassword($admin, $admin->password);
        $psycologue->password = $this->password_hasher->hashPassword($psycologue, $psycologue->password);
        $correcteur->password = $this->password_hasher->hashPassword($correcteur, $correcteur->password);

        return [$admin, $psycologue, $correcteur];
    }

    private
    function niveau_scolaire(): array
    {
        return [
            new NiveauScolaire(id: 0, indice: 1, nom: "CEP ou niveau CEP"),
            new NiveauScolaire(id: 0, indice: 2, nom: "CAP - BEP - BEPC"),
            new NiveauScolaire(id: 0, indice: 3, nom: "Niveau BAC (1e Terminale)"),
            new NiveauScolaire(id: 0, indice: 4, nom: "BAC"),
            new NiveauScolaire(id: 0, indice: 5, nom: "BAC + 1"),
            new NiveauScolaire(id: 0, indice: 6, nom: "BAC + 2 (DEUG)"),
            new NiveauScolaire(id: 0, indice: 7, nom: "Licence ou Maîtrise"),
            new NiveauScolaire(id: 0, indice: 8, nom: "Ingénieur ou 3e cycle"),
        ];
    }

    private
    function concours(): array
    {
        $result = [
            new Concours(id: 0,
                nom: "Comissaire de police",
                correcteurs: new ArrayCollection(),
                sessions: new ArrayCollection(),
                index_grille: GrilleRepository::GRILLE_OCTOBRE_2019_INDEX,
                type_concours: 0, version_batterie: 0, questions: new ArrayCollection()),
            new Concours(id: 0,
                nom: "Officier (lieutenant de police)",
                correcteurs: new ArrayCollection(),
                sessions: new ArrayCollection(),
                index_grille: GrilleRepository::GRILLE_OCTOBRE_2019_INDEX,
                type_concours: 0, version_batterie: 0, questions: new ArrayCollection()),
            new Concours(id: 0,
                nom: "Sélection spécialisée - Motard - Garde de sécurité ambassade",

                correcteurs: new ArrayCollection(),
                sessions: new ArrayCollection(),
                index_grille: GrilleRepository::GRILLE_OCTOBRE_2019_INDEX,
                type_concours: 0,
                version_batterie: 0,
                questions: new ArrayCollection()),
            new Concours(id: 0,
                nom: "Gardien de la paix",
                correcteurs: new ArrayCollection(),
                sessions: new ArrayCollection(),
                index_grille: GrilleRepository::GRILLE_OCTOBRE_2019_INDEX,
                type_concours: 0, version_batterie: 0, questions: new ArrayCollection()),
            new Concours(id: 0,
                nom: "BAC - sélection spécialisée",
                correcteurs: new ArrayCollection(),
                sessions: new ArrayCollection(),
                index_grille: GrilleRepository::GRILLE_OCTOBRE_2019_INDEX,
                type_concours: 0, version_batterie: 0, questions: new ArrayCollection()),
            new Concours(id: 0,
                nom: "Tests brigadier (Entrée en formation)",
                correcteurs: new ArrayCollection(),
                sessions: new ArrayCollection(),
                index_grille: GrilleRepository::GRILLE_OCTOBRE_2019_INDEX,
                type_concours: 0, version_batterie: 0, questions: new ArrayCollection()),
            new Concours(id: 0,
                nom: "Tests brigadier (EXAPRO BIER)",
                correcteurs: new ArrayCollection(),
                sessions: new ArrayCollection(),
                index_grille: GrilleRepository::GRILLE_OCTOBRE_2019_INDEX,
                type_concours: 0, version_batterie: 0, questions: new ArrayCollection()),
            new Concours(id: 0,
                nom: "Cadet de la république",
                correcteurs: new ArrayCollection(),
                sessions: new ArrayCollection(),
                index_grille: GrilleRepository::GRILLE_OCTOBRE_2019_INDEX,
                type_concours: 0, version_batterie: 0, questions: new ArrayCollection()),
            new Concours(id: 0,
                nom: "Adjoint de sécurité",
                correcteurs: new ArrayCollection(),
                sessions: new ArrayCollection(),
                index_grille: GrilleRepository::GRILLE_OCTOBRE_2019_INDEX,
                type_concours: 0, version_batterie: 0, questions: new ArrayCollection()),
        ];

        foreach ($result as $concours) {
            QuestionConcours::initQuestions($this->grille_repository, $concours);
        }

        return $result;
    }

    private
    function graphique(Profil $profil, int $render_index): Graphique
    {
        $renderer = $this->renderer_repository->fromIndex($render_index);

        $graphique = new Graphique(
            id: 0,
            options: $renderer->initializeOptions(),
            profil: $profil,
            echelles: new ArrayCollection(),
            nom: "Graphique d'exemple",
            renderer_index: $render_index
        );

        Graphique::initializeEchelles($graphique, $renderer);

        return $graphique;
    }

    private
    function sgaps(): array
    {
        return [
            new Sgap(id: 0, indice: 1, nom: "Bordeaux"),
            new Sgap(id: 0, indice: 2, nom: "Dijon"),
            new Sgap(id: 0, indice: 3, nom: "Lille"),
            new Sgap(id: 0, indice: 4, nom: "Lyon"),
            new Sgap(id: 0, indice: 5, nom: "Marseille"),
            new Sgap(id: 0, indice: 6, nom: "Metz"),
            new Sgap(id: 0, indice: 7, nom: "Rennes"),
            new Sgap(id: 0, indice: 8, nom: "Toulouse"),
            new Sgap(id: 0, indice: 9, nom: "Tours"),
            new Sgap(id: 0, indice: 10, nom: "Paris"),
            new Sgap(id: 0, indice: 11, nom: "Versailles"),
            new Sgap(id: 0, indice: 12, nom: "Guadeloupe"),
            new Sgap(id: 0, indice: 13, nom: "Guyane"),
            new Sgap(id: 0, indice: 14, nom: "Martinique"),
            new Sgap(id: 0, indice: 15, nom: "La Réunion"),
            new Sgap(id: 0, indice: 16, nom: "Saint-Pierre et Miquelon"),
            new Sgap(id: 0, indice: 17, nom: "Polynésie"),
            new Sgap(id: 0, indice: 18, nom: "Nouvelle Calédonie, Wallis et Futuna"),
            new Sgap(id: 0, indice: 19, nom: "Mayotte"),
            new Sgap(id: 0, indice: 70, nom: "Lognes BFIE")
        ];
    }

    private
    function echelles(): array
    {
        return [
            new Echelle(id: 0, nom: "Collationnement", nom_php: "collationnement", type: Echelle::TYPE_ECHELLE_SIMPLE),
            new Echelle(id: 0, nom: "Verbal mot", nom_php: "verbal_mot", type: Echelle::TYPE_ECHELLE_SIMPLE),
            new Echelle(id: 0, nom: "Spatial", nom_php: "spatial", type: Echelle::TYPE_ECHELLE_SIMPLE),
            new Echelle(id: 0,
                nom: "Verbal syntaxique",
                nom_php: "verbal_syntaxique",
                type: Echelle::TYPE_ECHELLE_SIMPLE),
            new Echelle(id: 0, nom: "Raisonnement", nom_php: "raisonnement", type: Echelle::TYPE_ECHELLE_SIMPLE),
            new Echelle(id: 0, nom: "Dic", nom_php: "dic", type: Echelle::TYPE_ECHELLE_SIMPLE),
            new Echelle(id: 0, nom: "Anxiete", nom_php: "anxiete", type: Echelle::TYPE_ECHELLE_SIMPLE),
            new Echelle(id: 0, nom: "Irritabilite", nom_php: "irritabilite", type: Echelle::TYPE_ECHELLE_SIMPLE),
            new Echelle(id: 0, nom: "Impusilvite", nom_php: "impusilvite", type: Echelle::TYPE_ECHELLE_SIMPLE),
            new Echelle(id: 0, nom: "Introspection", nom_php: "introspection", type: Echelle::TYPE_ECHELLE_SIMPLE),
            new Echelle(id: 0, nom: "Entetement", nom_php: "entetement", type: Echelle::TYPE_ECHELLE_SIMPLE),
            new Echelle(id: 0, nom: "Mefiance", nom_php: "mefiance", type: Echelle::TYPE_ECHELLE_SIMPLE),
            new Echelle(id: 0, nom: "Depression", nom_php: "depression", type: Echelle::TYPE_ECHELLE_SIMPLE),
            new Echelle(id: 0, nom: "Gene", nom_php: "gene", type: Echelle::TYPE_ECHELLE_SIMPLE),
            new Echelle(id: 0,
                nom: "Manque d'altruisme",
                nom_php: "manque_altruisme",
                type: Echelle::TYPE_ECHELLE_SIMPLE),
            new Echelle(id: 0, nom: "Sociabilite", nom_php: "sociabilite", type: Echelle::TYPE_ECHELLE_SIMPLE),
            new Echelle(id: 0, nom: "Spontaneite", nom_php: "spontaneite", type: Echelle::TYPE_ECHELLE_SIMPLE),
            new Echelle(id: 0, nom: "Ascendance", nom_php: "ascendance", type: Echelle::TYPE_ECHELLE_SIMPLE),
            new Echelle(id: 0, nom: "Assurance", nom_php: "assurance", type: Echelle::TYPE_ECHELLE_SIMPLE),
            new Echelle(id: 0,
                nom: "Interêt intelletuel",
                nom_php: "interet_intelletuel",
                type: Echelle::TYPE_ECHELLE_SIMPLE),
            new Echelle(id: 0, nom: "Nouveaute", nom_php: "nouveaute", type: Echelle::TYPE_ECHELLE_SIMPLE),
            new Echelle(id: 0, nom: "Creativite", nom_php: "creativite", type: Echelle::TYPE_ECHELLE_SIMPLE),
            new Echelle(id: 0, nom: "Rigueur", nom_php: "rigueur", type: Echelle::TYPE_ECHELLE_SIMPLE),
            new Echelle(id: 0, nom: "Planification", nom_php: "planification", type: Echelle::TYPE_ECHELLE_SIMPLE),
            new Echelle(id: 0, nom: "Perseverance", nom_php: "perseverance", type: Echelle::TYPE_ECHELLE_SIMPLE),
            new Echelle(id: 0, nom: "Sincerite", nom_php: "sincerite", type: Echelle::TYPE_ECHELLE_SIMPLE),
            new Echelle(id: 0, nom: "Obsessionalite", nom_php: "obsessionalite", type: Echelle::TYPE_ECHELLE_SIMPLE),
            new Echelle(id: 0, nom: "Agressivite", nom_php: "agressivite", type: Echelle::TYPE_ECHELLE_SIMPLE),
            new Echelle(id: 0, nom: "Depressivite", nom_php: "depressivite", type: Echelle::TYPE_ECHELLE_SIMPLE),
            new Echelle(id: 0, nom: "Paranoidie", nom_php: "paranoidie", type: Echelle::TYPE_ECHELLE_SIMPLE),
            new Echelle(id: 0, nom: "Narcissisme", nom_php: "narcissisme", type: Echelle::TYPE_ECHELLE_SIMPLE),
            new Echelle(id: 0,
                nom: "Intolerance à la frustration",
                nom_php: "intolerance_a_la_frustration",
                type: Echelle::TYPE_ECHELLE_SIMPLE),
        ];
    }
}