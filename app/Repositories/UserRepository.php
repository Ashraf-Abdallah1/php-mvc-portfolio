<?php

namespace App\Repositories;

use App\Models\User;
use Framework\Database;
use RuntimeException;

class UserRepository implements UserRepositoryInterface
{
    private Database $database;

    public function __construct(Database $database)
    {
        $this->database = $database;
    }

    public function all(): array
    {
        $users = [];

        $stmt = $this->database->prepare("SELECT * FROM Users");
        $stmt->execute();
        $rows = $stmt->fetchAll();

        foreach ($rows as $user) {
            $rowUser = $this->fromDbRow($user);
            $users[] = $rowUser;
        }

        return $users;
    }

    public function findById(int $id): ?User
    {
        return null;
    }

    public function findByUsername(string $username): ?User
    {
        $stmt = $this->database->prepare(
            "SELECT * FROM Users WHERE username = :username"
        );

        $stmt->execute([
            "username" => $username
        ]);

        $row = $stmt->fetch();

        if ($row === false) {
            return null;
        }

        return $this->fromDbRow($row);
    }

    public function insert(User $user): User
    {
        $stmt = $this->database->prepare(
            "INSERT INTO Users (name, username, password, role)
             VALUES (:name, :username, :password, :role)"
        );

        $stmt->execute([
            "name" => $user->getName(),
            "username" => $user->getUsername(),
            "password" => $user->getPassword(),
            "role" => $user->getRole(),
        ]);

        $id = $this->database->getLastID();
        $user->setId($id);

        return $user;
    }

    public function update(User $user): User
    {
        return new User();
    }

    public function fromDbRow(mixed $row): User
    {
        if ($row === null) {
            throw new RuntimeException("Invalid DB row for User");
        }

        $user = new User();
        $user->setName($row->name);
        $user->setUsername($row->username);
        $user->setPassword($row->password);
        $user->setId($row->id);
        $user->setRole($row->role ?? 'user');

        return $user;
    }
}
