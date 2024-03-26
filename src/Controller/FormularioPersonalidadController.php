<?php

namespace App\Controller;

use App\Entity\Personalidad;
use App\Form\FormularioPersonalidadType;
use App\Form\PersonalidadType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;


 #[Route('/formulario')]
class FormularioPersonalidadController extends AbstractController
{
    #[Route('/personalidad', name: 'app_formulario_personalidad')]
    public function index(): Response
    {
        return $this->render('formulario_personalidad/index.html.twig', [
            'controller_name' => 'FormularioPersonalidadController',
        ]);
    }

    #[Route('/nuevo', name: 'app_formulario_nuevo', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $personalidades = $entityManager->getRepository(Personalidad::class)->findAll();



        $form = $this->createForm(FormularioPersonalidadType::class, null, [
            'personalidades' => $personalidades
        ]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
//            $entityManager->persist($personalidad);
//            $entityManager->flush();

            return $this->redirectToRoute('app_personalidad_index', [], Response::HTTP_SEE_OTHER);
        }



        return $this->render('perfil/index.html.twig', [
            'form' => $form,
        ]);
    }


     /**
      * @param array $discPredominante
      * @return string
      */
     private function getTemplate(array $discPredominante): string
     {
         return match ($discPredominante[0]) {
             'D' => '/perfil/dominante.html.twig',
             'I' => '/perfil/influyente.html.twig',
             'S' => '/perfil/estable.html.twig',
             'C' => '/perfil/cauteloso.html.twig',
             default => '/perfil/index.html.twig',
         };
    }


}
