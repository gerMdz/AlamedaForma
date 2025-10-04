<?php

namespace App\DataFixtures;

use App\Entity\Habilidades;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;
use Doctrine\Persistence\ObjectManager;

class HabilidadesFixtures extends Fixture implements FixtureGroupInterface
{
    /**
     * [nombre, discripcion, identificador]
     */
    private const HABILIDADES = [
        ["Entretener", "Habilidad de actuar, danzar, hablar.", "entretener"],
        ["Reclutar", "Habilidad de enrolar y motivar la participación de la gente.", "reclutar"],
        ["Entrevistar", "Habilidad de  descubrir la forma de ser de las personas.", "entrevistar"],
        ["Investigar", "Habilidad de  leer, compilar información, recabar datos.", "investigar"],
        ["Dirigir", "Habilidad de  motivar a que la gente te sigua para así llegar a una meta común.", "dirigir"],
        ["Artística", "Habilidad de  conceptualizar, dibujar, pintar o fotografiar.", "artistica"],
        ["Gráficas", "Habilidad de  diseñar, crear ayudas visuales o rótulos.", "graficas"],
        ["Evaluar", "Habilidad de  analizar datos y obtener conclusiones.", "evaluar"],
        ["Planear", "Habilidad de  diseñar y organizar programas y eventos, presentar estrategias.", "planear"],
        ["Coordinar", "Habilidad de  supervisar a la gente para poder cumplir una tarea o evento y coordinar los detalles involucrados.", "coordinar"],
        ["Consejería", "Habilidad  de escuchar, exhortar y guiar con sensibilidad.", "consejeria"],
        ["Educar", "Habilidad de explicar, entrenar, demostrar.", "educar"],
        ["Escribir", "Habilidad de  escribir artículos, cartas y libros.", "escribir"],
        ["Editar", "Habilidad de  editar, mecanografiar textos.", "editar"],
        ["Promover", "Habilidad de  promover y publicar eventos.", "promover"],
        ["Reparar", "Habilidad de  arreglar, restaurar, mantener.", "reparar"],
        ["Alimentar", "Habilidad de  cocinar para grupos grandes y pequeños.", "alimentar"],
        ["Recordar", "Habilidad de  recordar nombres y rostros.", "recordar"],
        ["Operar Maquinaria", "Habilidad de operar máquinas y herramientas.", "operar"],
        ["Ahorrar", "Habilidad de buscar y encontrar materiales y recursos baratos.", "ahorrar"],
        ["Contar", "Habilidad:  trabajar con números, datos o dinero.", "contar"],
        ["Clasificar", "Habilidad de  sistematizar y archivar libros, datos, registros y materiales para poder encontrarlos fácilmente.", "clasificar"],
        ["Relaciones Públicas", "Habilidad de  tratar con quejas y clientes no satisfechos con cuidado y cortesía.", "relaciones-publicas"],
        ["Saludar", "Habilidad de  expresar calidez, desarrollar y hacer a otros sentirse bien.", "saludar"],
        ["Compositor", "Habilidad de escribir música o letra.", "compositor"],
        ["Jardinería", "Habilidad:  de trabajar con plantas y jardines.", "jardines"],
        ["Artes y Manualidades", "Habilidad de decoración, arreglo de lugares para eventos especiales.", "artes-manualidades"],
        ["Musical", "Habilidades  dirigir a la congregación con voz y/o instrumento.", "musical"],
        ["Otro", " ", "otro"]
    ];

    public function load(ObjectManager $manager): void
    {
        foreach (self::HABILIDADES as $data) {
            $hab = new Habilidades();
            $hab->setNombre($data[0]);
            $hab->setDiscripcion($data[1]);
            $hab->setIdentificador($data[2]);
            $manager->persist($hab);
        }

        $manager->flush();
    }

    public static function getGroups(): array
    {
        return ['groupHabilidades'];
    }
}
