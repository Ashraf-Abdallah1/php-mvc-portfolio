<?php

namespace App\Repositories;

use App\Models\User;
use Framework\Database;

class UserRepository implements UserRepositoryInterface
{
    private Database $database;

    public function __construct(Database $database)
    {
        $this->database = $database;
    }

    public function all(): array
    {
        $stmt = $this->database->run("SELECT * FROM Users")->fetchAll();
        $users = [];
        foreach ($stmt as $user) {
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
        $stmt = $this->database->run("SELECT * FROM Users WHERE username = :username", ["username" => $username])->fetch();

        if ($stmt === false) {
            return null;
        }

        return $this->fromDbRow($stmt);
    }

    public function insert(User $user): User
    {
        $stmt = $this->database->run(
            "INSERT INTO Users (name, username, password) VALUES (:name, :username, :password)",
            [
                "name" => $user->getName(),
                "username" => $user->getUsername(),
                "password" => $user->getPassword(),
            ]
        );
        return new User();
    }

    public function update(User $user): User
    {
        return new User();
    }

    public function fromDbRow(mixed $row): User
    {
        $user = new User();
        $user->setName($row->name);
        $user->setUsername($row->username);
        $user->setPassword($row->password);
        $user->setId($row->id);
        return $user;
    }
}
