<?php

namespace App\Controller\Admin;

use App\Entity\Personalidad;
use App\Form\PersonalidadType;
use App\Repository\PersonalidadRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/personalidad')]
class PersonalidadController extends AbstractController
{
    #[Route('/', name: 'app_personalidad_index', methods: ['GET'])]
    public function index(PersonalidadRepository $personalidadRepository): Response
    {
        return $this->render('personalidad/index.html.twig', [
            'personalidads' => $personalidadRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_personalidad_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $personalidad = new Personalidad();
        $form = $this->createForm(PersonalidadType::class, $personalidad);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($personalidad);
            $entityManager->flush();

            return $this->redirectToRoute('app_personalidad_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('personalidad/new.html.twig', [
            'personalidad' => $personalidad,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_personalidad_show', methods: ['GET'])]
    public function show(Personalidad $personalidad): Response
    {
        return $this->render('personalidad/show.html.twig', [
            'personalidad' => $personalidad,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_personalidad_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Personalidad $personalidad, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(PersonalidadType::class, $personalidad);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_personalidad_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('personalidad/edit.html.twig', [
            'personalidad' => $personalidad,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_personalidad_delete', methods: ['POST'])]
    public function delete(Request $request, Personalidad $personalidad, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$personalidad->getId(), $request->request->get('_token'))) {
            $entityManager->remove($personalidad);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_personalidad_index', [], Response::HTTP_SEE_OTHER);
    }
}
