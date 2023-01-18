<?php

namespace App\Constraint;

use App\Core\Correcteur\CorrecteurManager;
use App\Core\Grille\GrilleRepository;
use App\Entity\Correcteur;
use App\Entity\Echelle;
use App\Entity\Profil;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Symfony\Component\Form\Exception\UnexpectedTypeException;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class MatchingEchellesValidator extends ConstraintValidator
{

    public function __construct(
        private readonly EntityManagerInterface $entity_manager
    )
    {
    }

    /**
     * @param mixed $value
     * @param MatchingEchelles $constraint
     * @return void
     */
    public function validate(mixed $value, Constraint $constraint): void
    {
        if (!$constraint instanceof MatchingEchelles) {
            throw new UnexpectedTypeException($constraint, MatchingEchelles::class);
        }

        $profil_property_name = $constraint->profil_property_name;

        /** @var Profil $profil */
        $profil = $value->$profil_property_name;

        $echelles_property_name = $constraint->echelles_property_name;

        /** @var Collection $echelles_collection */
        $echelles_collection = $value->$echelles_property_name;

        $sub_echelle_property_name = $constraint->sub_echelle_property_name;

        // Vérifie que toutes les echelles du profil existent, les ajouter sinon
        /** @var Echelle $echelle */
        foreach ($profil->echelles as $echelle) {

            $exists = false;

            foreach ($echelles_collection as $object_echelle) {

                /** @var Echelle $object_sub_schelle */
                $object_sub_schelle = $object_echelle->$sub_echelle_property_name;
                if ($object_sub_schelle->id == $echelle->id) {
                    $exists = true;
                    break;
                }
            }

            if (!$exists) {


                // TODO $value->$echelles_property_name[] = $constraint->init->call($value, $echelle);


            }
        }

        throw new Exception("TODO");
    }
}