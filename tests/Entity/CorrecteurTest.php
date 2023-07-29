<?php

namespace App\Tests\Entity;

use App\Core\Grille\Values\GrilleOctobre2019;
use App\Entity\Concours;
use App\Entity\Correcteur;
use App\Entity\Structure;
use App\Repository\GrilleRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Exception;
use PHPUnit\Framework\TestCase;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Bundle\MakerBundle\Validator;
use Symfony\Component\Validator\Test\ConstraintValidatorTestCase;
use Symfony\Component\Validator\Validator\RecursiveValidator;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\Validator\ValidatorBuilder;
use Twig\Test\IntegrationTestCase;

class CorrecteurTest extends KernelTestCase
{
    /**
     * @throws Exception
     */
    public function testValidation()
    {

        self::bootKernel();

        /** @var ValidatorInterface $validator */
        $validator = self::getContainer()->get("validator");

        $correcteur = new Correcteur(
            id: 0,
            concours: new Concours(id: 0,
                nom: "Concours",
                correcteurs: new ArrayCollection(),
                sessions: new ArrayCollection(),
                index_grille: GrilleRepository::GRILLE_OCTOBRE_2019_INDEX,
                type_concours: 0, version_batterie: 0, questions: new ArrayCollection()),
            structure: new Structure(
                id: 0,
                nom: "Structure",
                echelles: new ArrayCollection(),
                etalonnages: new ArrayCollection(),
                graphiques: new ArrayCollection()
            ),
            nom: "Correcteur",
            echelles: new ArrayCollection()
        );

        self::assertEmpty($validator->validate($correcteur));
    }
}
