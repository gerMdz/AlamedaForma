<?php

namespace App\Controller\Api;

use App\Entity\AvanceForma;
use App\Entity\FormularioHabilitacion;
use App\Entity\Personales;
use App\Repository\AvanceFormaRepository;
use App\Repository\FormularioHabilitacionRepository;
use App\Repository\PersonalesRepository;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/api/forma')]
class AvanceFormaPublicController extends AbstractController
{
    public function __construct(
        private readonly PersonalesRepository $personalesRepo,
        private readonly FormularioHabilitacionRepository $formRepo,
        private readonly AvanceFormaRepository $avanceRepo,
        private readonly EntityManagerInterface $em,
    ) {}

    /**
     * Consulta si la persona ya aceptó los Términos (identifier = "T").
     */
    #[Route('/terminos-estado/{personalId}', name: 'api_forma_terminos_estado', methods: ['GET'])]
    #[IsGranted('PUBLIC_ACCESS')]
    public function terminosEstado(string $personalId): JsonResponse
    {
        $persona = $this->personalesRepo->find($personalId);
        if (!$persona) {
            return new JsonResponse(['error' => 'Persona no encontrada'], 404);
        }
        $formT = $this->formRepo->findOneBy(['identifier' => 'T'], ['activoDesde' => 'DESC']);
        if (!$formT) {
            // Si no existe config de T, consideramos no aceptado para forzar mostrar términos
            return new JsonResponse(['accepted' => false], 200);
        }
        $existing = $this->avanceRepo->findOneBy(['persona' => $persona, 'formulario' => $formT]);
        return new JsonResponse(['accepted' => (bool)$existing], 200);
    }

    /**
     * Registra la aceptación de Términos y condiciones (identifier = "T") para una persona.
     * Crea un AvanceForma si aún no existe para esta combinación.
     */
    #[Route('/aceptar-terminos', name: 'api_forma_aceptar_terminos', methods: ['POST'])]
    #[IsGranted('PUBLIC_ACCESS')]
    public function aceptarTerminos(Request $request): JsonResponse
    {
        $payload = json_decode((string) $request->getContent(), true);
        if (!is_array($payload)) {
            return new JsonResponse(['error' => 'JSON inválido'], 400);
        }

        $personalId = $payload['personalId'] ?? null;
        if (!$personalId) {
            return new JsonResponse(['error' => 'personalId es requerido'], 400);
        }

        /** @var Personales|null $persona */
        $persona = $this->personalesRepo->find($personalId);
        if (!$persona) {
            return new JsonResponse(['error' => 'Persona no encontrada'], 404);
        }

        // Buscar el formulario de Términos por identifier = "T"; preferimos el más reciente por activoDesde
        /** @var FormularioHabilitacion|null $formT */
        $formT = $this->formRepo->findOneBy(['identifier' => 'T'], ['activoDesde' => 'DESC']);
        if (!$formT) {
            return new JsonResponse(['error' => 'Formulario de Términos (T) no configurado'], 404);
        }

        // Idempotencia: si ya existe registro para esta persona y este formulario, no duplicar
        $existing = $this->avanceRepo->findOneBy(['persona' => $persona, 'formulario' => $formT]);
        if ($existing) {
            return new JsonResponse([
                'ok' => true,
                'alreadyAccepted' => true,
                'fechaEtapa' => $existing->getFechaEtapa()?->format(DATE_ATOM),
            ], 200);
        }

        $avance = (new AvanceForma())
            ->setPersona($persona)
            ->setFormulario($formT)
            ->setFechaEtapa(new DateTimeImmutable());

        $this->em->persist($avance);
        $this->em->flush();

        return new JsonResponse([
            'ok' => true,
            'alreadyAccepted' => false,
            'fechaEtapa' => $avance->getFechaEtapa()->format(DATE_ATOM),
        ], 201);
    }
}
