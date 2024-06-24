<?php

namespace App\DataFixtures;

use App\Entity\Instructions;
use App\Entity\Organization;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;
use Doctrine\Persistence\ObjectManager;

class InstructionsFixtures extends Fixture implements FixtureGroupInterface
{
    
    // <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12" style="text-align: justify;"

    public string $title = "<h1>Instrucciones - Conociendo tu F.O.R.M.A.</h1>";
    public string $content = "<p>1. Escribe el punto al cual asistes, tu nombre y dirección de correo electrónico en la sección a continuación, <strong>asegúrate que la dirección de correo sea la correcta.</strong></p>
                                <br>
                                <p>2. Esta no es una prueba. ¡Disfruta el proceso! No hay respuestas correctas o incorrectas, solo hechos verdaderos y claros acerca de tu experiencia y preferencia de acuerdo con las declaraciones que encontrará en las siguientes secciones. Los resultados son simplemente un reflejo de tus fortalezas</p>
                                <br>
                                <p>3. Nuestro equipo de liderazgo en tu punto, te contactará para conversar contigo sobre los resultados y también para invitarte a conocer más de nuestra iglesia.</p>
                                <br>
                                <p><strong>NOTA 1:</strong> Esta evaluación dura entre 30 y 60 minutos y debe completarse en una sola sesión. <strong><br>NOTA 2:</strong> Esta evaluación funciona mejor en una computadora de escritorio o laptop, y no en un teléfono.</p>
                            ";

    public function load(ObjectManager $manager): void
    {
        $organization = $manager->getRepository(Organization::class)->findAll();


        $instruction = new Instructions();
        $instruction
            ->setTitle($this->title)
            ->setContent($this->content)
            ->setEnabled(true)
            ->setOrganization($organization[array_rand($organization)])
        ;
        $manager->persist($instruction);

        $manager->flush();
    }
    public function getDependencies(): array
    {
        return [
            HOrganizationFixtures::class,
        ];
    }

    public static function getGroups(): array
    {
        return ['groupInstructions'];
    }
}
