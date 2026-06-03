<?php

namespace App\Services;

use App\Models\User;
use App\Repositories\UserRepositoryInterface;
use Framework\Session;

class AuthService
{
    private UserRepositoryInterface $userRepository;

    private Session $session;

    public function __construct(UserRepositoryInterface $userRepository, Session $session)
    {
        $this->userRepository = $userRepository;
        $this->session = $session;
    }

    public function register(User $user, string $password): User
    {
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        $user->setPassword($hashedPassword);

        if ($user->getRole() === '') {
            $user->setRole('user');
        }
        return $this->userRepository->insert($user);
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
            $errors['password'] = "Onbekende gebruiker of wachtwoord onjuist";
            return $errors;
        }

        $this->session->regenerate();
        $this->session->set('user_id', $user->getId());
        $this->session->set('username', $username);
        $this->session->set('role', $user->getRole());


        return $user;
    }

    public function logout(): void
    {
        $_SESSION = [];
        if (ini_get("session.use_cookies")) {
            $params = session_get_cookie_params();
            setcookie(
                (string)session_name(),
                '',
                time() - 42000,
                $params["path"],
                $params["domain"],
                $params["secure"],
                $params["httponly"]
            );
        }
        $this->session->destroy();
    }

    public function isLoggedIn(): bool
    {
        return $this->session->get('user_id') !== null;
    }

    public function getUserId(): ?int
    {
        $id = $this->session->get('user_id');
        return $id !== null ? (int)$id : null;
    }

    public function getRole(): ?string
    {
        return $this->session->get('role');
    }

    public function isAdmin(): bool
    {
        return $this->session->get('role') === 'admin';
    }
}
