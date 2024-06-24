<?php

namespace App\DataFixtures;

use App\Entity\Organization;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class HOrganizationFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
         $organization = new Organization();

         $organization->setName('Société Générale')
             ->setIdentifier('SG')
         ;
         $manager->persist($organization);

        $manager->flush();
    }
}
