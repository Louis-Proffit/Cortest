<?php

namespace App\Fixture;

use App\Entity\Concours;
use App\Entity\CortestUser;
use App\Entity\NiveauScolaire;
use App\Entity\QuestionConcours;
use App\Entity\Sgap;
use App\Repository\GrilleRepository;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class InitFixture extends Fixture
{

    public function __construct(
        private readonly UserPasswordHasherInterface $password_hasher,
        private readonly GrilleRepository            $grille_repository
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

        $all_concours = $this->concours();
        foreach ($all_concours as $concours) {
            $manager->persist($concours);
        }

        $niveaux_scolaire = $this->niveau_scolaire();
        foreach ($niveaux_scolaire as $niveau_scolaire) {
            $manager->persist($niveau_scolaire);
        }

        foreach ($this->users() as $user) {
            $manager->persist($user);
        }

        $manager->flush();
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
                nom: "Comissaire de police [444]",
                correcteurs: new ArrayCollection(),
                sessions: new ArrayCollection(),
                index_grille: GrilleRepository::GRILLE_OCTOBRE_2019_INDEX,
                type_concours: '41', version_batterie: '444', questions: new ArrayCollection()),
            new Concours(id: 0,
                nom: "Officier (lieutenant de police) [003]",
                correcteurs: new ArrayCollection(),
                sessions: new ArrayCollection(),
                index_grille: GrilleRepository::GRILLE_OCTOBRE_2019_INDEX,
                type_concours: '31', version_batterie: '003', questions: new ArrayCollection()),
            new Concours(id: 0,
                nom: "Sélection spécialisée - Motard - Garde de sécurité ambassade [003]",
                correcteurs: new ArrayCollection(),
                sessions: new ArrayCollection(),
                index_grille: GrilleRepository::GRILLE_OCTOBRE_2019_INDEX,
                type_concours: '31',
                version_batterie: '003',
                questions: new ArrayCollection()),
            new Concours(id: 0,
                nom: "Gardien de la paix [333]",
                correcteurs: new ArrayCollection(),
                sessions: new ArrayCollection(),
                index_grille: GrilleRepository::GRILLE_OCTOBRE_2019_INDEX,
                type_concours: '01', version_batterie: '333', questions: new ArrayCollection()),
            new Concours(id: 0,
                nom: "BAC - sélection spécialisée [222] ",
                correcteurs: new ArrayCollection(),
                sessions: new ArrayCollection(),
                index_grille: GrilleRepository::GRILLE_OCTOBRE_2019_INDEX,
                type_concours: '1', version_batterie: '222', questions: new ArrayCollection()),
            new Concours(id: 0,
                nom: "Cadet de la République [111]",
                correcteurs: new ArrayCollection(),
                sessions: new ArrayCollection(),
                index_grille: GrilleRepository::GRILLE_OCTOBRE_2019_INDEX,
                type_concours: '61', version_batterie: '111', questions: new ArrayCollection()),
            new Concours(id: 0,
                nom: "PA [111]",
                correcteurs: new ArrayCollection(),
                sessions: new ArrayCollection(),
                index_grille: GrilleRepository::GRILLE_OCTOBRE_2019_INDEX,
                type_concours: '61', version_batterie: '111', questions: new ArrayCollection()),
            new Concours(id: 0,
                nom: "Technicien de la PTS [333]",
                correcteurs: new ArrayCollection(),
                sessions: new ArrayCollection(),
                index_grille: GrilleRepository::GRILLE_OCTOBRE_2019_INDEX,
                type_concours: '285', version_batterie: '333', questions: new ArrayCollection()),
            new Concours(id: 0,
                nom: "Technicien Principal (TPPTS) [003]",
                correcteurs: new ArrayCollection(),
                sessions: new ArrayCollection(),
                index_grille: GrilleRepository::GRILLE_OCTOBRE_2019_INDEX,
                type_concours: '461', version_batterie: '003', questions: new ArrayCollection()),
            new Concours(id: 0,
                nom: "Ingénieur [444]",
                correcteurs: new ArrayCollection(),
                sessions: new ArrayCollection(),
                index_grille: GrilleRepository::GRILLE_OCTOBRE_2019_INDEX,
                type_concours: '42', version_batterie: '444', questions: new ArrayCollection()),
        ];

        foreach ($result as $concours) {
            QuestionConcours::initQuestions($this->grille_repository, $concours);
        }

        return $result;
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
}