<?php

namespace App\Fixture;

use App\Entity\CortestUser;
use App\Entity\NiveauScolaire;
use App\Entity\Sgap;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class InitFixture extends Fixture implements FixtureGroupInterface
{

    public function __construct(
        private readonly UserPasswordHasherInterface $userPasswordHasher
    )
    {
    }

    public
    function load(ObjectManager $manager): void
    {
        $sgaps = $this->sgaps();
        foreach ($sgaps as $sgap) {
            $manager->persist($sgap);
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

        $psychologue = new CortestUser(
            id: 0,
            username: "psychologue",
            password: "UvsTr8L7Fz76m4",
            role: CortestUser::ROLE_PSYCHOLOGUE
        );

        $correcteur = new CortestUser(
            id: 0,
            username: "correcteur",
            password: "rvsUTF8L776zm4",
            role: CortestUser::ROLE_CORRECTEUR
        );

        $admin->password = $this->userPasswordHasher->hashPassword($admin, $admin->password);
        $psychologue->password = $this->userPasswordHasher->hashPassword($psychologue, $psychologue->password);
        $correcteur->password = $this->userPasswordHasher->hashPassword($correcteur, $correcteur->password);

        return [$admin, $psychologue, $correcteur];
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

    public static function getGroups(): array
    {
        return ["init"];
    }
}