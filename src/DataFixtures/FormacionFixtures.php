<?php

namespace App\DataFixtures;

use App\Entity\Dones;
use App\Entity\Formacion;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;
use Doctrine\Persistence\ObjectManager;

class FormacionFixtures extends Fixture implements FixtureGroupInterface
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
                ['10', 'Con frecuencia tengo ideas dentro de mí que ofrecen soluciones prácticas a problemas difíciles. ', (string)$this->dones[9]],
                ['28', 'Dios me ha habilitado para elegir correctamente entre muchas opciones complejas en una decisión importante, cuando nadie más sabe qué hacer.', (string)$this->dones[9]],
                ['46', 'La gente con frecuencia busca mi consejo cuando no saben qué hacer en una situación.', (string)$this->dones[9]],
                ['64', 'Dios me ha habilitado para aplicar apropiadamente las verdades bíblicas y la perspectiva de Dios en situaciones de la vida.', (string)$this->dones[9]],

                ['11', 'Me gusta animar y dar consejo a los que están desanimados o flaqueando en la fe.', (string)$this->dones[10]],
                ['29', 'Me siento muy realizado cuando animo a otros, especialmente si se trata de su crecimiento espiritual.', (string)$this->dones[10]],
                ['47', 'Siento una necesidad de desafiar a otros para que mejoren, especialmente en su crecimiento espiritual, levantándolos y no condenándolos.', (string)$this->dones[10]],
                ['65', 'Tengo la habilidad para estimular lo mejor de otros y desafiarlos a que desarrollen su potencial.', (string)$this->dones[10]],

                ['12', 'Tengo una habilidad de estudiar a fondo un pasaje de las Escrituras y luego compartirlo con otros.', (string)$this->dones[11]],
                ['30', 'Tengo la habilidad para educar al pueblo de Dios explicándoles claramente la Biblia, motivándoles a que de ella aprendan.', (string)$this->dones[11]],
                ['48', 'Otros escuchan y disfrutan mis enseñanzas de las Escrituras.', (string)$this->dones[11]],
                ['66', 'Soy sistemático en acercarme a un grupo de personas a presentarles lecciones bíblicas.', (string)$this->dones[11]],

                ['13', 'Tengo la responsabilidad del crecimiento espiritual de uno o más jóvenes cristianos en este momento.', (string)$this->dones[12]],
                ['31', 'Me gusta estar involucrado en la vida de las personas y ayudarles a crecer espiritualmente.', (string)$this->dones[12]],
                ['49', 'Me preocupo por el bienestar espiritual de la gente y hago mi mejor esfuerzo para guiarlos a una vida conforme a Dios.', (string)$this->dones[12]],
                ['67', 'Ayudo a cristianos que se han apartado del Señor a encontrar su camino de regreso a una relación creciente con Él y a involucrarse en una iglesia local.', (string)$this->dones[12]],

                ['14', 'Otras personas me respetan como una autoridad en asuntos espirituales.', (string)$this->dones[13]],
                ['32', 'Estaría dispuesto y emocionado de empezar una nueva iglesia.', (string)$this->dones[13]],
                ['50', 'Tengo la habilidad para empezar nuevas iglesias y supervisar su desarrollo como una autoridad espiritual.', (string)$this->dones[13]],
                ['68', 'Me emocionaría mucho compartir el evangelio y formar nuevos grupos de cristianos en áreas donde no hay muchas iglesias.', (string)$this->dones[13]],

                ['15', 'Tengo habilidad de aprender lenguas extranjeras.', (string)$this->dones[14]],
                ['33', 'Me puedo adaptar fácilmente a culturas, lenguajes y estilos de vida diferentes a los míos, y me gustaría usar mi adaptabilidad para ministrar en países extranjeros.', (string)$this->dones[14]],
                ['51', 'Me gustaría presentar el evangelio en una lengua extranjera, en otro país que no sea el mío.', (string)$this->dones[14]],
                ['69', 'No tengo prejuicios hacia otras personas.', (string)$this->dones[14]],

                ['16', 'Con frecuencia Dios me revela la dirección en la que Él desea que se mueva el cuerpo de Cristo.', (string)$this->dones[15]],
                ['34', 'Suelo hablar muy bien de los principios cristianos con convicción, aun cuando esto no es popular.', (string)$this->dones[15]],
                ['52', 'Siento necesidad de hablar los mensajes bíblicos de Dios, así la gente sabrá lo que Dios espera de ellos.', (string)$this->dones[15]],
                ['70', 'Encuentro relativamente fácil aplicar promesas bíblicas a situaciones presentes, y estoy dispuesto a confrontar en amor, si es necesario.', (string)$this->dones[15]],

                ['17', 'Disfruto desarrollar relaciones con personas que no son cristianas con la esperanza de compartir de Jesús y guiar a la gente a responder con fe.', (string)$this->dones[16]],
                ['35', 'Me es muy fácil invitar a una persona a aceptar a Cristo como su Salvador.', (string)$this->dones[16]],
                ['53', 'Me gusta crear oportunidades para compartir a Cristo en una forma positiva y no amenazante.', (string)$this->dones[16]],
                ['71', 'Tengo un fuerte deseo de ayudar a los no cristianos a encontrar salvación por medio de Jesucristo.', (string)$this->dones[16]],

                ['18', 'Cuando oigo de necesidades, siento la carga de orar.', (string)$this->dones[17]],
                ['36', 'Tengo la pasión de orar por asuntos significativos para el reino de Dios y su voluntad para los cristianos.', (string)$this->dones[17]],
                ['54', 'Muchas de mis oraciones por otros han sido contestadas por el Señor.', (string)$this->dones[17]],
                ['72', 'La oración es mi ministerio favorito en la iglesia, y consistentemente paso mucho tiempo en ella. ', (string)$this->dones[17]],


            ];
    }

    public function getDependencies(): array
    {
        return [
            DonesFixtures::class,
        ];
    }

    public function load(ObjectManager $manager): void
    {
        $don = null;
        foreach ($this->formaciones as $formation) {

            if($don !== $formation[2]) {
                $donEntity = $manager->getRepository(Dones::class)->findOneBy(['name' => $formation[2]]);
            }
            $formaciones = new Formacion();
            $formaciones->setOrden($formation[0])
                ->setDescription($formation[1])
                ->setIdentifier(strtolower($formation[2]).'_'.$formation[0])
                ->setDescription($formation[1])
                ->setParent($formation[2])
                ->setDonAssociate($donEntity)
            ;
            $manager->persist($formaciones);
            $don = $formation[2];
        }


        $manager->flush();
    }

    public static function getGroups(): array
    {
        return ['groupDones'];
    }
}
