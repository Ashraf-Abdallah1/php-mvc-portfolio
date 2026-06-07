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
        $name = (string)$request->get('name');

        $errors = $this->checkInputs($username, $password);

        if (!empty($errors)) {
            return $this->response->view('user/register.html.twig', [
                'errors' => $errors,
                'old' => [
                    'username' => $username,
                    'name' => $name,
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

    public function loginGuest(Request $request): Response
    {
        $output = $this->authService->login("guest", "guest");
        return $this->response->redirect('/');
    }

    public function logout(Request $request): Response
    {
        $this->authService->logout();
        return $this->response->redirect('/login');
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
        } else {
            if (strlen($password) < 8) {
                $errors['password'] = "Password moet minimaal 8 tekens bevatten";
            }

            if (!preg_match('/[A-Z]/', $password)) {
                $errors['password'] = "Password moet minstens één hoofdletter bevatten";
            }

            if (!preg_match('/[a-z]/', $password)) {
                $errors['password'] = "Password moet minstens één kleine letter bevatten";
            }

            if (!preg_match('/[0-9]/', $password)) {
                $errors['password'] = "Password moet minstens één cijfer bevatten";
            }

            if (!preg_match('/[\W]/', $password)) {
                $errors['password'] = "Password moet minstens één speciaal teken bevatten";
            }
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
