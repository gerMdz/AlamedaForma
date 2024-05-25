<?php

namespace App\DataFixtures;

use App\Entity\Inicio;
use App\Entity\Organization;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class PInicioFixtures extends Fixture
{
    public string $terms = '<h4> TÉRMINOS Y CONDICIONES PARA LA REALIZACIÓN DEL TEST F.O.R.M.A</h4>
<ol>
<li> Al iniciar con el llenado del Test F.O.R.M.A de manera VOLUNTARIA y NO OBLIGATORIA, declara que ha leído detenidamente, entendido y aceptado los Términos y Condiciones.</li>
<li> Al iniciar con el llenado del Test F.O.R.M.A que Société Générale le presenta, declara conocer y aceptar la Política de Privacidad.</li>
<li> Podrá tener acceso al Test F.O.R.M.A en el link: www.example.com .</li>
<li> Acepta y reconoce que el Test NO ES REQUISITO que Société Générale le solicita para asistir ni servir dentro de la Iglesia.</li>
<li> Acepta y reconoce que el Test F.O.R.M.A NO ES UNA EVALUACIÓN PSICOLÓGICA PROFESIONAL, únicamente es una herramienta que funciona para brindarle una orientación del servicio ministerial que podría desempeñar dentro de la Iglesia, tomando como base el resultado del Test.</li>
<li> Acepta y reconoce que el Test F.O.R.M.A va dirigido a toda persona que tenga en su corazón el deseo de servir en cualquiera de los Ministerios de Société Générale.</li>
<li> La información contenida en el Test se utilizará con fines ministeriales, y si decide ser parte de nuestro voluntariado; se adjuntará como un elemento a su expediente ministerial. Los pastores y líderes de Société Générale, pueden utilizar esta información para fines privados con la persona en términos de promociones, capacitaciones o involucramiento en otras áreas de servicio.</li>
<li> Acepta y reconoce que usted será el único responsable de sus respuestas.</li>
<li> No podrá compartir por ningún medio digital, medio conocido o por conocerse el instructivo y contenido del Test F.O.R.M.A; así como, de los resultados obtenidos para trasladarlos a miembros de la iglesia, pastores, líderes, voluntarios, terceros u otras personas que deseen servir dentro de la Iglesia.</li>
<li> Société Générale no podrá cambiar o modificar las respuestas contenidas en el Test.</li>
<li> Acepta y reconoce que las preguntas formuladas fueron creadas tomando en cuenta que goza de una vocación, experiencias, habilidades, cursos recibidos y que ha tenido contribución en este mundo, ya sea en el área espiritual o material.</li>
<li> Cada área del Test contará con un instructivo que detallará las diferentes actividades que se irán desarrollando.</li>
<li> Acepta y reconoce que al finalizar el Test se le generará automáticamente un resultado conforme a sus respuestas.</li>
<li> 14. Acepta y reconoce que el resultado obtenido en el Test servirá para orientarlo en el servicio ministerial que podría desempeñar dentro de la Iglesia de forma voluntaria.</li>
</ol>
<h4> POLÍTICA DE PRIVACIDAD</h4>
<ol>
<li> Toda información personal, respuestas y resultados serán eminentemente confidenciales.</li>
<li> El Test estará conectado a un Software de Voluntarios CMS (Church Management System/Sistema de Administración de la Iglesia) en el que únicamente Société Générale tendrá acceso.</li>
<li> Société Générale tomará vías viables y razonables que se encuentren a su alcance, para proteger la información proporcionada mediante procesos y prácticas diseñadas para la protección de la información y así evitar ciberataques, hackeos, daños o accesos no autorizados.</li>
<li> Toda la información contenida dentro del Test no podrá ser utilizada ni compartida con terceras personas fuera de nuestra iglesia; ni serán divulgadas, publicadas o comercializadas en medios digitales, plataformas digitales o cualquier medio de reproducción conocido o por conocerse.</li>
<li> La información proporcionada no será utilizada para fines ajenos al propósito de la creación del Test F.O.R.M.A</li>
<li> Los resultados serán enviados únicamente por correo electrónico a: La persona que llena el Test, Pastor de Punto y Propósito de Servicio; por lo que no será compartido a terceras personas.</li>
<li> Los resultados quedarán almacenados en una base de datos de Voluntarios, el cual será utilizado únicamente por Société Générale; mismos que servirán como antecedentes, si la persona que llenó el formulario decide en un futuro servir y pueda ser orientado por parte de la Iglesia.</li>
<li> La información proporcionada únicamente será utilizada con el fin de orientar a la persona para el servicio ministerial conforme a su diseño; y así, de esa manera agilizar su ubicación en cualquiera de los Ministerios de la Iglesia.</li>
</ol>
';
    
    public string $content = 'Con el propósito de brindar una orientación a las personas que desean servir en cualquiera de los Ministerios de Société Générale, hemos desarrollado una herramienta que te ayudará a conocer tu diseño dado por Dios, es decir tu F.O.R.M.A; cuyo acróstico representa 5 áreas, siendo: 1. Formación Espiritual, 2. Orientación del Corazón, 3. Recursos y Habilidades, 4. Mi Personalidad y 5. Antecedentes. Esta herramienta te brindará una orientación ministerial, que te ayudará a expresar de mejor manera el diseño que Dios puso en ti; nuestro objetivo es que cada persona sirva con amor, siguiendo el modelo de Jesús, sabiendo que las capacidades las da Él y por último, contar con la actitud correcta; el resultado de esto te ayudará a descubrir el propósito de su vida.
Previo a realizar este test, le solicitamos proceda a leer los Términos y Condiciones; luego de haber finalizado con la lectura y si se encuentra de acuerdo, favor dar clic en ACEPTO LOS TÉRMINOS Y CONDICIONES.';
    private string $title = 'Descubre tu Propósito, conociendo tu F.O.R.M.A';

    public function load(ObjectManager $manager): void
    {
        $organization = $manager->getRepository(Organization::class)->findAll();


        $inicio = new Inicio();
         $inicio
             ->setTitle($this->title)
             ->setContent($this->content)
             ->setTerms($this->terms)
             ->setOrganization($organization[array_rand($organization)])
         ;
         $manager->persist($inicio);

        $manager->flush();
    }
    public function getDependencies(): array
    {
        return [
            OrganizationFixtures::class,
        ];
    }
}
