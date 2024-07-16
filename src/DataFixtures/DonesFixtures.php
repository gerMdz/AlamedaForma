<?php

namespace App\DataFixtures;

use App\Entity\Dones;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;
use Doctrine\Persistence\ObjectManager;

class DonesFixtures extends Fixture implements FixtureGroupInterface
{
    const DONES =  [
        ["Ayuda", "La habilidad de trabajar con, y apoyar los esfuerzos de otros ministerios cristianos."],
        ["Liderazgo", "Habilidad de clarificar y comunicar el propósito y la dirección (visión) de un ministerio en una manera que atraiga otros a incluirse. Es la habilidad para motivar a otros por ejemplo propio, a trabajar juntos para alcanzar la meta propuesta."],
        ["Hospitalidad", "La habilidad de hacer que las personas se sientan 'en casa', bienvenidos, cuidados y parte de un grupo. Es la habilidad para coordinar actividades que promueven el compañerismo."],
        ["Servicio", "La habilidad para reconocer necesidades insatisfechas en la familia de la iglesia, y tomar la iniciativa para proveer asistencia práctica rápida y alegremente y sin necesidad de reconocimiento. La habilidad de identificar y suplir las necesidades prácticas de otros."],
        ["Administración", "Es la habilidad para reconocer los dones de otros y reclutarlos para el ministerio. Es la habilidad para organizar y administrar personas, recursos y tiempo para un trabajo más efectivo. La habilidad para coordinar muchos detalles y ejecutar los planes de liderazgo."],
        ["Discernimiento", "Es la habilidad para distinguir lo bueno y lo malo, la verdad de la mentira, y hacer una evaluación inmediata basada en la Palabra de Dios. Es la habilidad para discernir si la fuente de una experiencia es Satanás, el ego (fuentes humanas), o el Espíritu Santo."],
        ["Fe", "Es la habilidad para confiar en Dios por aquello que no puede ser visto y de actuar basado en las promesas de Dios, a pesar de lo que la circunstancia indique."],
        ["Dar", "Es la habilidad para contribuir generalmente con recursos materiales y/o dinero (más allá del diezmo), de manera que el cuerpo de Cristo pueda crecer y ser fortalecido. Es la habilidad para ganar y administrar dinero de manera que pueda ser dado para apoyar el ministerio de otros."],
        ["Misericordia", "La habilidad de sentir sincera empatía y compasión en una manera que resulta de alivio práctico para las heridas de las personas, para su dolor y su sufrimiento. Por la habilidad de empatía con los demás, proveen apoyo compasivo y animador a aquellos q lo necesitan."],
        ["Sabiduría", "Es la habilidad para explicar cómo hacer las cosas, tomar decisiones correctas y ayudar a otros a moverse en la dirección adecuada según la perspectiva de Dios y compartirlas de una forma simple y comprensible."],
        ["Exhortación", "La habilidad para motivar al pueblo de Dios a aplicar los principios bíblicos y actuar en base a ellos. Comunicar apropiadamente palabras de ánimo, desafío o reprensión al cuerpo de Cristo especialmente cuando está desanimado o flaqueando en la fe. Estimular el potencial de cada uno."],
        ["Enseñanza", "La habilidad de educar al pueblo de Dios explicándoles claramente la Biblia, motivándoles a que aprendan de ella. Es la habilidad para equipar y entrenar a otros creyentes para el ministerio."],
        ["Pastor", "Es la habilidad para cuidar por las necesidades espirituales de un grupo de creyentes, y equiparlos para el ministerio. Es la habilidad para 'nutrir' un grupo en crecimiento espiritual y asumir la responsabilidad por su bienestar."],
        ["Apóstol", "La habilidad para empezar nuevas iglesias y de ser pionero de ministerios, liderarlos y supervisar su desarrollo."],
        ["Misionero", "La habilidad de adaptarse y ministrar de manera efectiva a diferentes culturas más allá de la suya propia para poder alcanzar a no creyentes y ayudar a los creyentes de esas culturas."],
        ["Profecía", "La habilidad para comunicar la Palabra de Dios públicamente, de declarar humildemente la verdad de Dios, a pesar de las consecuencias, en una forma inspirada, que convence a los no creyentes y desafía y conforta a los creyentes. Declara persuasivamente el propósito de Dios."],
        ["Evangelismo", "La habilidad de compartir y comunicar las buenas nuevas de Jesucristo con otros, tanto creyentes como no creyentes, en una forma positiva y no amenazante. Crea oportunidades para compartir a Cristo y guiar a la gente a responder con fe."],
        ["Intercesión", "Es la habilidad de orar por las necesidades de los demás por largos períodos de tiempo en base regular. Es la habilidad para persistir en oración y no desanimarse hasta que la respuesta llegue."]
    ];

    public function load(ObjectManager $manager): void
    {
        foreach (self::DONES as $don) {
            $dones = new Dones();
            $dones->setName($don[0]);
            $dones->setDescription($don[1]);
            $dones->setIdentifier(strtolower($don[0]));
            $manager->persist($dones);
        }


        $manager->flush();
    }

    public static function getGroups(): array
    {
        return ['groupDones'];
    }
}
