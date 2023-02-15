<?php

namespace App\Fixture;

use App\Entity\Concours;
use App\Entity\Session;
use App\Entity\Sgap;
use DateTime;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\DependencyInjection\Attribute\When;

#[When("test")]
class TestFixture extends Fixture implements DependentFixtureInterface
{


    public function load(ObjectManager $manager)
    {
        // TODO ?
    }

    public function getDependencies(): array
    {
        return [
            InitFixture::class
        ];
    }
}
