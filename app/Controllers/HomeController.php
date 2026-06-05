<?php

namespace App\Controllers;

use App\Services\AuthService;
use Framework\Response;
use Framework\ResponseFactory;

class HomeController
{
    private ResponseFactory $responseFactory;

    private AuthService $authService;

    public function __construct(ResponseFactory $responseFactory, AuthService $authService)
    {
        $this->responseFactory = $responseFactory;
        $this->authService = $authService;
    }

    public function index(): Response
    {
        if (!$this->authService->isLoggedIn()) {
            return $this->responseFactory->redirect('/login');
        }
        return $this->responseFactory->view('index.html.twig', [
            'active' => "home",
        ]);
    }
}
