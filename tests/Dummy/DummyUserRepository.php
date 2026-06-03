<?php

namespace Dummy;

use App\Models\User;
use App\Repositories\UserRepositoryInterface;

class DummyUserRepository implements UserRepositoryInterface
{
    private DummyDatabase $db;

    public function __construct(DummyDatabase $db)
    {
        $this->db = $db;
    }

    public function insert(User $user): User
    {
        $id = $this->db->insertUser([
            'name' => $user->getName(),
            'username' => $user->getUsername(),
            'password' => $user->getPassword(),
            'role' => $user->getRole(),
        ]);

        $user->setId($id);
        return $user;
    }

    public function findByUsername(string $username): ?User
    {
        $row = $this->db->findUserByUsername($username);

        if (!$row) {
            return null;
        }

        $user = new User();
        $user->setId($row['id']);
        $user->setName($row['name']);
        $user->setUsername($row['username']);
        $user->setPassword($row['password']);
        $user->setRole($row['role']);

        return $user;
    }

    public function all(): array
    {
        $rows = $this->db->allUsers();
        $users = [];

        foreach ($rows as $row) {
            $user = new User();
            $user->setId($row['id']);
            $user->setName($row['name']);
            $user->setUsername($row['username']);
            $user->setPassword($row['password']);
            $user->setRole($row['role']);
            $users[] = $user;
        }

        return $users;
    }

    public function findById(int $id): ?User
    {
        return null;
    }

    public function update(User $user): User
    {
        return new User();
    }
}
