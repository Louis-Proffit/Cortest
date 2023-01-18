<?php

namespace App\Tests\Entity;

use App\Core\Grille\Values\GrilleOctobre2019;
use App\Entity\Correcteur;
use App\Entity\Profil;
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
            grille_class: GrilleOctobre2019::class,
            profil: new Profil(
                id: 0,
                nom: "Profil",
                echelles: new ArrayCollection(),
                etalonnages: new ArrayCollection(),
                graphiques: new ArrayCollection()
            ),
            nom: "Correcteur",
            echelles: new ArrayCollection()
        );

        self::assertEmpty($validator->validateProperty($correcteur, "nom"));
        self::assertEmpty($validator->validateProperty($correcteur, "grille_class"));
    }
}
