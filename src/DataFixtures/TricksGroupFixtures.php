<?php

namespace App\DataFixtures;

use App\Entity\TricksGroup;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;

class TricksGroupFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $names = array(
            'Les grabs',
            'Les rotations',
            'Les flips',
            'Les rotations désaxées',
            'Les slides',
            'Les one foot tricks',
            'Old school'
        );

        foreach ($names as $name) {
            $tricksGroup = new TricksGroup();
            $tricksGroup->setTitle($name);

            $manager->persist($tricksGroup);
        }

        $manager->flush();
    }
}
