<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class FormacionFixtures extends Fixture
{
    public array $dones = [
        'Ayuda',
        'Liderazgo',
        'Hospitalidad',
        'Servicio',
        'Administración',
        'Discernimiento',
        'Fe',
        'Dar',
        'Misericordia',
        'Sabiduría',
        'Exhortación',
        'Enseñanza',
        'Pastor',
        'Apóstol',
        'Misionero',
        'Profecía',
        'Evangelismo',
        'Intercesión',
    ];

    public function getDones(): array
    {
        return $this->dones;
    }
    
    protected array $formaciones;



    public function __construct()
    {
        $this->formaciones =
            [
                ['1', 'Disfruto trabajar detrás de la escena, ocupándome de detalles pequeños.', (string)$this->dones[0]],
                ['19', 'Me gustaría ayudar a pastores y otros líderes para que ellos se puedan enfocar en sus prioridades ministeriales.', (string)$this->dones[0]],
                ['37', 'Me gusta ayudar a otros en tareas rutinarias para que puedan terminar proyectos importantes.', (string)$this->dones[0]],
                ['55', 'Me gusta ayudar a otros a terminar su trabajo, y no necesito mucho reconocimiento público.', (string)$this->dones[0]],
                ['2', 'Por lo general doy un paso adelante y tomo el liderazgo en un grupo en el cual no lo hay.', (string)$this->dones[1]],
                ['20', 'Cuando le pido a la gente que me ayude con un ministerio importante de la iglesia, usualmente dicen que sí.', (string)$this->dones[1]],
                ['38', 'Puedo guiar y motivar a un grupo de personas a lograr una meta específica.', (string)$this->dones[1]],
                ['56', 'La gente respeta mi opinión y sigue mis instrucciones.', (string)$this->dones[1]],
                ['3', 'Cuando estoy en un grupo, tiendo a darme cuenta de aquellos que están solos y les ayudo a sentirse parte del grupo.', (string)$this->dones[2]],
                ['21', 'Me gusta entretener a los invitados y hacerles sentir “en casa” cuando están de visita.', (string)$this->dones[2]],
                ['39', 'Me gusta coordinar actividades de compañerismo en las cuales las personas se sientan bienvenidas, aceptadas y confortables.', (string)$this->dones[2]],
                ['57', 'Me gustaría usar mi casa para conocer a los recién llegados y a los visitantes de la iglesia.', (string)$this->dones[2]],
                ['4', 'Tengo la habilidad de reconocer una necesidad y de hacer el trabajo sin importar lo pequeña que sea la tarea.', (string)$this->dones[3]],
                ['22', 'Tengo iniciativa de servir a otros y lo disfruto, sin esperar reconocimiento.', (string)$this->dones[3]],
                ['40', 'Soy muy confiable para hacer las cosas a tiempo y no necesito que me lo agradezcan ni tampoco necesito mucha alabanza.', (string)$this->dones[3]],
                ['58', 'Me gusta ayudar a la gente en cualquier tipo de necesidad y siento satisfacción al suplirla.', (string)$this->dones[3]],
                ['5', 'Tengo la habilidad de organizar ideas, personas y proyectos para alcanzar una meta específica.', (string)$this->dones[4]],
                ['23', 'Soy una persona muy organizada y hago planes, establezco metas y las alcanzo.', (string)$this->dones[4]],
                ['41', 'Fácilmente delego grandes responsabilidades a otras personas.', (string)$this->dones[4]],
                ['59', 'Me gusta organizar y administrar personas, recursos y tiempo para un ministerio más efectivo.', (string)$this->dones[4]],
                ['6', 'Con frecuencia se dice que tengo un buen criterio espiritual, discerniendo si la fuente de una experiencia es Satanás, el ego o el Espíritu Santo.', (string)$this->dones[5]],
                ['24', 'Soy buen juez del carácter y puedo descubrir a un farsante espiritual.', (string)$this->dones[5]],
                ['42', 'Puedo distinguir entre lo correcto y lo incorrecto en asuntos espirituales complejos cuando otras personas parecen no darse cuenta.', (string)$this->dones[5]],
                ['60', 'La gente viene a mí por ayuda cuando necesitan distinguir entre una verdad espiritual y un error.', (string)$this->dones[5]],
                ['7', 'Tengo mucha confianza de lograr grandes cosas para el reino de Dios a pesar de las circunstancias.', (string)$this->dones[6]],
                ['25', 'Por lo general doy un paso adelante y empiezo proyectos que otras personas no se atreven, y usualmente tengo éxito, porque tengo la disponibilidad para arriesgarme a fallar en la búsqueda de una visión dada por Dios.', (string)$this->dones[6]],
                ['43', 'Yo confío en la fidelidad de Dios para un buen futuro aun cuando estoy enfrentando grandes problemas.', (string)$this->dones[6]],
                ['61', 'Frecuentemente ejercito mi fe por medio de la oración, y Dios contesta mis oraciones de manera poderosa.', (string)$this->dones[6]],
                ['8', 'Me agrada dar dinero a los que están en serias necesidades financieras.', (string)$this->dones[7]],
                ['26', 'Con gusto doy dinero adicional a mi diezmo a la iglesia para que el cuerpo de Cristo pueda crecer y ser fortalecido.', (string)$this->dones[7]],
                ['44', 'No me importaría bajar mi nivel de vida para dar más a la iglesia y a otros en necesidad y con frecuencia doy anónimamente.', (string)$this->dones[7]],
                ['62', 'Tengo la habilidad de ganar dinero y administrarlo de manera que pueda ser dado para apoyar el ministerio de otro.', (string)$this->dones[7]],
                ['9', 'Me gusta ministrar a personas en hospitales, cárceles o asilos para consolarlos.', (string)$this->dones[8]],
                ['27', 'Siento compasión por las personas que están heridas y solas y me gusta pasar un buen tiempo con ellas para animarlas.', (string)$this->dones[8]],
                ['45', 'Detecto fácilmente el dolor y son empático con aquellos que sufren en la familia de la Iglesia.', (string)$this->dones[8]],
                ['63', 'Me gusta dar apoyo compasivo y animador a aquellos que experimentan desgracia, crisis o dolor.', (string)$this->dones[8]],
            ];
        }

    public function load(ObjectManager $manager): void
    {
        // $product = new Product();
        // $manager->persist($product);

        $manager->flush();
    }
}
