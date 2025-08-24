<?php

namespace App\Controller\Admin;

use App\Entity\Instructions;
use App\Form\InstructionsType;
use App\Repository\InstructionsRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/admin/instrucciones')]
#[IsGranted('ROLE_USER')]
class InstructionsController extends AbstractController
{
    #[Route('/', name: 'app_instructions_index', methods: ['GET'])]
    public function index(InstructionsRepository $repo): Response
    {
        return $this->render('instructions/index.html.twig', [
            'items' => $repo->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_instructions_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $em): Response
    {
        $item = new Instructions();
        $form = $this->createForm(InstructionsType::class, $item);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($item);
            $em->flush();
            return $this->redirectToRoute('app_instructions_index');
        }

        return $this->render('instructions/new.html.twig', [
            'form' => $form,
            'item' => $item,
        ]);
    }

    #[Route('/{id}', name: 'app_instructions_show', methods: ['GET'])]
    public function show(Instructions $item): Response
    {
        return $this->render('instructions/show.html.twig', [
            'item' => $item,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_instructions_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Instructions $item, EntityManagerInterface $em): Response
    {
        $form = $this->createForm(InstructionsType::class, $item);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->flush();
            return $this->redirectToRoute('app_instructions_index');
        }

        return $this->render('instructions/edit.html.twig', [
            'form' => $form,
            'item' => $item,
        ]);
    }

    #[Route('/{id}', name: 'app_instructions_delete', methods: ['POST'])]
    public function delete(Request $request, Instructions $item, EntityManagerInterface $em): Response
    {
        if ($this->isCsrfTokenValid('delete'.$item->getId(), $request->getPayload()->get('_token'))) {
            $em->remove($item);
            $em->flush();
        }

        return $this->redirectToRoute('app_instructions_index');
    }

    #[Route('/{id}/update', name: 'app_instructions_update_api', methods: ['PUT', 'PATCH'])]
    public function updateApi(Request $request, Instructions $item, EntityManagerInterface $em): JsonResponse
    {
        $data = json_decode($request->getContent() ?: '[]', true) ?? [];

        if (array_key_exists('title', $data)) {
            $item->setTitle((string) $data['title']);
        }
        if (array_key_exists('content', $data)) {
            $item->setContent((string) $data['content']);
        }
        if (array_key_exists('enabled', $data)) {
            $item->setEnabled((bool) $data['enabled']);
        }

        $em->flush();

        return $this->json([
            'success' => true,
            'id' => (string) $item->getId(),
            'title' => $item->getTitle(),
            'enabled' => $item->isEnabled(),
        ]);
    }
}
