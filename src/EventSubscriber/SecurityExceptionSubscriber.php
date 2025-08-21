<?php

namespace App\EventSubscriber;

use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\Security\Core\Exception\InsufficientAuthenticationException;
use Symfony\Bundle\SecurityBundle\Security;

/**
 * Logs a production warning/alert whenever an authentication/authorization error occurs,
 * specifically: AccessDeniedException, InsufficientAuthenticationException, and HTTP 401.
 */
class SecurityExceptionSubscriber implements EventSubscriberInterface
{
    private const TARGET_MESSAGE = 'Full authentication is required to access this resource.';

    public function __construct(
        private readonly LoggerInterface $logger,
        private readonly Security $security,
    ) {}

    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::EXCEPTION => ['onKernelException', 10],
        ];
    }

    public function onKernelException(ExceptionEvent $event): void
    {
        // Only generate aviso in production environment
        $env = getenv('APP_ENV') ?: ($_SERVER['APP_ENV'] ?? ($_ENV['APP_ENV'] ?? 'prod'));
        if ('prod' !== $env) {
            return;
        }

        $exception = $event->getThrowable();

        if (!$this->isTargetException($exception)) {
            return;
        }

        $request = $event->getRequest();
        $context = $this->buildContext($request, $exception);

        // Use alert for visibility in production logs
        $this->logger->alert('Auth/Security exception detected in production', $context);
    }

    private function isTargetException(\Throwable $e): bool
    {
        if ($e instanceof AccessDeniedException) {
            return true;
        }
        if ($e instanceof InsufficientAuthenticationException) {
            return true;
        }
        if ($e instanceof HttpExceptionInterface && 401 === $e->getStatusCode()) {
            return true;
        }
        // In case different exception bubbles up but with the known message
        if (str_contains($e->getMessage(), self::TARGET_MESSAGE)) {
            return true;
        }

        return false;
    }

    /**
     * Build a rich context for logs: route, path, method, ip, user, exception details
     */
    private function buildContext(Request $request, \Throwable $e): array
    {
        $user = $this->security->getUser();

        return [
            'exception_class' => $e::class,
            'message' => $e->getMessage(),
            'code' => method_exists($e, 'getStatusCode') ? ($e->getStatusCode()) : $e->getCode(),
            'route' => $request->attributes->get('_route'),
            'path' => $request->getPathInfo(),
            'method' => $request->getMethod(),
            'ip' => $request->getClientIp(),
            'user' => $user?->getUserIdentifier() ?? 'anon',
            'firewall' => $request->attributes->get('_security_firewall_run') ?? null,
            'query' => $request->query->all(),
            // keep body out to avoid logging credentials; include content length only
            'content_length' => $request->headers->get('content-length'),
        ];
    }
}
