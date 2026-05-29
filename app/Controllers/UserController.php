<?php

namespace App\Controllers;

use App\Models\User;
use App\Repositories\UserRepositoryInterface;
use App\Services\AuthService;
use Framework\Request;
use Framework\Response;
use Framework\ResponseFactory;
use phpDocumentor\GraphViz\Exception;

class UserController
{
    private ResponseFactory $response;
    private UserRepositoryInterface $userRepository;

    private AuthService $authService;

    public function __construct(
        ResponseFactory $response,
        UserRepositoryInterface $userRepository,
        AuthService $authService,
    ) {
        $this->response = $response;
        $this->userRepository = $userRepository;
        $this->authService = $authService;
    }

    public function registerForm(Request $request): Response
    {
        return $this->response->view('user/register.html.twig');
    }

    public function register(Request $request): Response
    {
        $user = new User();
        $username = (string)$request->get('username');
        $password = (string)$request->get('password');

        $errors = $this->checkInputs($username, $password);

        if (!empty($errors)) {
            return $this->response->view('user/register.html.twig', [
                'errors' => $errors,
                'old' => [
                    'username' => $username
                ]
            ]);
        }

        $user->setName((string)$request->get('name'));
        $user->setUsername($username);
        $this->authService->register($user, $password);
        return $this->response->redirect('/');
    }

    public function loginForm(Request $request): Response
    {
        return $this->response->view('user/login.html.twig');
    }

    public function login(Request $request): Response
    {
        $username = (string)$request->get('username');
        $password = (string)$request->get('password');
        $output = $this->authService->login($username, $password);

        if ($output instanceof User) {
            return $this->response->redirect('/');
        }

        return $this->response->view('user/login.html.twig', [
            'errors' => $output,
            'old' => [
                'username' => $username,
                'password' => $password
            ]
        ]);
    }

    /**
     * @param string $username
     * @param string $password
     * @return array<string, string>
     */
    private function checkInputs(string $username, string $password): array
    {
        $errors = [];

        if (empty($username)) {
            $errors['username'] = "Username is verplicht";
        }

        if (!preg_match('/^[a-zA-Z0-9_]+$/', $username)) {
            $errors['username'] = "Username bevat onbekende tekens";
        }

        if (empty($password)) {
            $errors['password'] = "Password is verplicht";
        }

        $users = $this->userRepository->all();
        foreach ($users as $user) {
            if ($username === $user->getUsername()) {
                $errors['username'] = "Username bestaat al";
            }
        }

        return $errors;
    }
}
