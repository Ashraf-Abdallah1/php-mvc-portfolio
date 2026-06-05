<?php

namespace App\Middleware;

use App\Services\AuthService;
use Framework\Request;
use Framework\Response;
use Framework\ResponseFactory;

class AuthMiddleware
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
        $publicRoutes = [
            '/',
            '/login',
            '/register'
        ];

        if (in_array($request->path, $publicRoutes)) {
            return $next($request);
        }

        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        if (!$this->authService->isLoggedIn()) {
            return $this->responseFactory->view('errors/401.html.twig');
        }

        return $next($request);
    }
}
