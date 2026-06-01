<?php

namespace App\Middleware;

use App\Services\AuthService;
use Framework\Request;
use Framework\Response;
use Framework\ResponseFactory;

class AdminMiddleware
{
    private AuthService $authService;

    private ResponseFactory $responseFactory;

    public function __construct(AuthService $authService, ResponseFactory $responseFactory)
    {
        $this->authService = $authService;
        $this->responseFactory = $responseFactory;
    }

    public function handle(Request $request, callable $next): Response
    {
        $adminRoutes = [
            '#^/blogs/\d+/edit$#',
            '#^/blogs/\d+/deleteConfirm$#',
            '#^/blogs/\d+/delete$#',
        ];

        $path = $request->path;

        $isAdminRoute = false;

        foreach ($adminRoutes as $pattern) {
            if (preg_match($pattern, $path)) {
                $isAdminRoute = true;
                break;
            }
        }

        if (!$isAdminRoute) {
            return $next($request);
        }

        if (!$this->authService->isAdmin()) {
            return $this->responseFactory->view('errors/403.html.twig', [
                'message' => 'Je hebt geen toegang tot deze pagina',
                'user' => $this->authService->getUserId()
            ]);
        }

        return $next($request);
    }
}
