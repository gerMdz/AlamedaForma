<?php

namespace App\Controller\Api;

use App\Entity\AvanceForma;
use App\Entity\FormularioHabilitacion;
use App\Entity\Personales;
use App\Repository\AvanceFormaRepository;
use App\Repository\FormularioHabilitacionRepository;
use App\Repository\PersonalesRepository;
use App\Repository\PersonalOrientacionRepository;
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
    /**
     * Registra avance para el formulario de Formación (identifier = "F").
     * Idempotente: si ya existe AvanceForma para la persona y el formulario F, responde 200.
     */
    #[Route('/registrar-avance-f', name: 'api_forma_registrar_avance_f', methods: ['POST'])]
    #[IsGranted('PUBLIC_ACCESS')]
    public function registrarAvanceF(Request $request): JsonResponse
    {
        $payload = json_decode((string) $request->getContent(), true);
        if (!is_array($payload)) {
            return new JsonResponse(['error' => 'JSON inválido'], 400);
        }
        $personalId = $payload['personalId'] ?? null;
        if (!$personalId) {
            return new JsonResponse(['error' => 'personalId es requerido'], 400);
        }
        $persona = $this->personalesRepo->find($personalId);
        if (!$persona) {
            return new JsonResponse(['error' => 'Persona no encontrada'], 404);
        }
        $formF = $this->formRepo->findOneBy(['identifier' => 'F'], ['activoDesde' => 'DESC']);
        if (!$formF) {
            return new JsonResponse(['error' => 'Formulario de Formación (F) no configurado'], 404);
        }
        $existing = $this->avanceRepo->findOneBy(['persona' => $persona, 'formulario' => $formF]);
        if ($existing) {
            return new JsonResponse([
                'ok' => true,
                'alreadyRegistered' => true,
                'fechaEtapa' => $existing->getFechaEtapa()?->format(DATE_ATOM),
            ], 200);
        }
        $avance = (new AvanceForma())
            ->setPersona($persona)
            ->setFormulario($formF)
            ->setFechaEtapa(new \DateTimeImmutable());
        $this->em->persist($avance);
        $this->em->flush();
        return new JsonResponse([
            'ok' => true,
            'alreadyRegistered' => false,
            'fechaEtapa' => $avance->getFechaEtapa()->format(DATE_ATOM),
        ], 201);
    }
    public function __construct(
        private readonly PersonalesRepository $personalesRepo,
        private readonly FormularioHabilitacionRepository $formRepo,
        private readonly AvanceFormaRepository $avanceRepo,
        private readonly EntityManagerInterface $em,
        private readonly PersonalOrientacionRepository $personalOrientacionRepo,
    ) {}

    /**
     * Consulta si la persona ya avanzó en Formación (identifier = "F").
     */
    #[Route('/avance-f-estado/{personalId}', name: 'api_forma_avance_f_estado', methods: ['GET'])]
    #[IsGranted('PUBLIC_ACCESS')]
    public function avanceFEstado(string $personalId): JsonResponse
    {
        $persona = $this->personalesRepo->find($personalId);
        if (!$persona) {
            return new JsonResponse(['error' => 'Persona no encontrada'], 404);
        }
        $formF = $this->formRepo->findOneBy(['identifier' => 'F'], ['activoDesde' => 'DESC']);
        if (!$formF) {
            return new JsonResponse(['hasAvanceF' => false], 200);
        }
        $existing = $this->avanceRepo->findOneBy(['persona' => $persona, 'formulario' => $formF]);
        return new JsonResponse(['hasAvanceF' => (bool)$existing], 200);
    }

    /**
     * Consulta si la persona ya completó Orientación (identifier = "O").
     */
    #[Route('/avance-o-estado/{personalId}', name: 'api_forma_avance_o_estado', methods: ['GET'])]
    #[IsGranted('PUBLIC_ACCESS')]
    public function avanceOEstado(string $personalId): JsonResponse
    {
        $persona = $this->personalesRepo->find($personalId);
        if (!$persona) {
            return new JsonResponse(['error' => 'Persona no encontrada'], 404);
        }
        $formO = $this->formRepo->findOneBy(['identifier' => 'O'], ['activoDesde' => 'DESC']);
        if (!$formO) {
            return new JsonResponse(['hasAvanceO' => false], 200);
        }
        $existing = $this->avanceRepo->findOneBy(['persona' => $persona, 'formulario' => $formO]);
        return new JsonResponse(['hasAvanceO' => (bool)$existing], 200);
    }

    /**
     * Consulta si la persona ya completó Recursos (identifier = "R").
     */
    #[Route('/avance-r-estado/{personalId}', name: 'api_forma_avance_r_estado', methods: ['GET'])]
    #[IsGranted('PUBLIC_ACCESS')]
    public function avanceREstado(string $personalId): JsonResponse
    {
        $persona = $this->personalesRepo->find($personalId);
        if (!$persona) {
            return new JsonResponse(['error' => 'Persona no encontrada'], 404);
        }
        $formR = $this->formRepo->findOneBy(['identifier' => 'R'], ['activoDesde' => 'DESC']);
        if (!$formR) {
            return new JsonResponse(['hasAvanceR' => false], 200);
        }
        $existing = $this->avanceRepo->findOneBy(['persona' => $persona, 'formulario' => $formR]);
        return new JsonResponse(['hasAvanceR' => (bool)$existing], 200);
    }

    /**
     * Registra avance para Orientación (identifier = "O"). Idempotente.
     */
    #[Route('/registrar-avance-o', name: 'api_forma_registrar_avance_o', methods: ['POST'])]
    #[IsGranted('PUBLIC_ACCESS')]
    public function registrarAvanceO(Request $request): JsonResponse
    {
        $payload = json_decode((string)$request->getContent(), true);
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
        /** @var FormularioHabilitacion|null $formO */
        $formO = $this->formRepo->findOneBy(['identifier' => 'O'], ['activoDesde' => 'DESC']);
        if (!$formO) {
            return new JsonResponse(['error' => 'Formulario de Orientación (O) no configurado'], 404);
        }
        $existing = $this->avanceRepo->findOneBy(['persona' => $persona, 'formulario' => $formO]);
        if ($existing) {
            return new JsonResponse([
                'ok' => true,
                'alreadyRegistered' => true,
                'fechaEtapa' => $existing->getFechaEtapa()?->format(DATE_ATOM),
            ], 200);
        }
        // Intentar tomar la fecha de creación de PersonalOrientacion si existe
        $po = $this->personalOrientacionRepo->findOneBy(['persona' => $persona]);
        $fecha = $po?->getCreatedAt() ?: new DateTimeImmutable();
        $avance = (new AvanceForma())
            ->setPersona($persona)
            ->setFormulario($formO)
            ->setFechaEtapa($fecha);
        $this->em->persist($avance);
        $this->em->flush();
        return new JsonResponse([
            'ok' => true,
            'alreadyRegistered' => false,
            'fechaEtapa' => $avance->getFechaEtapa()->format(DATE_ATOM),
        ], 201);
    }

    /**
     * Registra avance para Recursos (identifier = "R"). Idempotente.
     */
    #[Route('/registrar-avance-r', name: 'api_forma_registrar_avance_r', methods: ['POST'])]
    #[IsGranted('PUBLIC_ACCESS')]
    public function registrarAvanceR(Request $request): JsonResponse
    {
        $payload = json_decode((string)$request->getContent(), true);
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
        /** @var FormularioHabilitacion|null $formR */
        $formR = $this->formRepo->findOneBy(['identifier' => 'R'], ['activoDesde' => 'DESC']);
        if (!$formR) {
            return new JsonResponse(['error' => 'Formulario de Recursos (R) no configurado'], 404);
        }
        $existing = $this->avanceRepo->findOneBy(['persona' => $persona, 'formulario' => $formR]);
        if ($existing) {
            return new JsonResponse([
                'ok' => true,
                'alreadyRegistered' => true,
                'fechaEtapa' => $existing->getFechaEtapa()?->format(DATE_ATOM),
            ], 200);
        }
        $avance = (new AvanceForma())
            ->setPersona($persona)
            ->setFormulario($formR)
            ->setFechaEtapa(new DateTimeImmutable());
        $this->em->persist($avance);
        $this->em->flush();
        return new JsonResponse([
            'ok' => true,
            'alreadyRegistered' => false,
            'fechaEtapa' => $avance->getFechaEtapa()->format(DATE_ATOM),
        ], 201);
    }

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
