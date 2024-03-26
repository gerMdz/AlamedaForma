<?php

namespace App\DataFixtures;

use App\Entity\Personalidad;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class PersonalidadFixtures extends Fixture
{
    public array $personalidades = [
        ["Enérgico", "Vivaz", "Modesto", "Discreto",],
        ["Agresivo", "Emotivo", "Complaciente", "Constante",],
        ["Directo", "Animoso", "Agradable", "Acertado",],
        ["Tenaz", "Compasivo", "Dócil", "Perfeccionista",],
        ["Atrevido", "Impulsivo", "Amable", "Precavido",],
        ["Competitivo", "Expresivo", "Sustentador", "Preciso",],
        ["Arriesgado", "Hablador", "Relajado", "Objetivo",],
        ["Argumentador", "Divertido", "Paciente", "Lógico",],
        ["Audaz", "Espontáneo", "Estable", "Organizado",],
        ["Dirigente", "Optimista", "Apacible", "Concienzudo",],
        ["Cándido", "Alegre", "Leal", "Serio",],
        ["Independiente", "Entusiasta", "Buen oidor", "Altas norma"],

    ];

    public function load(ObjectManager $manager): void
    {
        foreach ($this->personalidades as $personalidades) {
            $personalidad = new Personalidad();

            $personalidad->setD($personalidades[0]);
            $personalidad->setI($personalidades[1]);
            $personalidad->setS($personalidades[2]);
            $personalidad->setC($personalidades[3]);

            $manager->persist($personalidad);
        }

        $manager->flush();
    }
}
