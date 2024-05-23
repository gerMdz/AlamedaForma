<?php

namespace App\Controller;

use App\Entity\Inicio;
use App\Form\InicioType;
use App\Repository\InicioRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/inicio')]
class InicioController extends AbstractController
{
    #[Route('/', name: 'app_inicio_index', methods: ['GET'])]
    public function index(InicioRepository $inicioRepository): Response
    {
        return $this->render('inicio/index.html.twig', [
            'inicios' => $inicioRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_inicio_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $inicio = new Inicio();
        $form = $this->createForm(InicioType::class, $inicio);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($inicio);
            $entityManager->flush();

            return $this->redirectToRoute('app_inicio_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('inicio/new.html.twig', [
            'inicio' => $inicio,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_inicio_show', methods: ['GET'])]
    public function show(Inicio $inicio): Response
    {
        return $this->render('inicio/show.html.twig', [
            'inicio' => $inicio,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_inicio_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Inicio $inicio, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(InicioType::class, $inicio);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_inicio_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('inicio/edit.html.twig', [
            'inicio' => $inicio,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_inicio_delete', methods: ['POST'])]
    public function delete(Request $request, Inicio $inicio, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$inicio->getId(), $request->getPayload()->get('_token'))) {
            $entityManager->remove($inicio);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_inicio_index', [], Response::HTTP_SEE_OTHER);
    }
}
