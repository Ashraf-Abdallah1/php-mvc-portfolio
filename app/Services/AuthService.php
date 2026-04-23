<?php

namespace App\Services;

use App\Models\User;
use App\Repositories\UserRepository;

class AuthService
{
    private UserRepository $userRepository;

    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    public function register(User $user, string $password): User
    {
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        $user->setPassword($hashedPassword);
        $this->userRepository->insert($user);
        return $user;
    }

    /**
     * @param string $username
     * @param string $password
     * @return User|array<string, string>
     */
    public function login(string $username, string $password): User|array
    {
        $errors = [];
        $user = $this->userRepository->findByUsername($username);
        if ($user == null) {
            $errors['username'] = "Username $username niet gevonden";
            return $errors;
        }
        if (!password_verify($password, $user->getPassword())) {
            $errors['password'] = "Incorrect password";
            return $errors;
        }

        return $user;
    }
}
