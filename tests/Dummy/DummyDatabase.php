<?php

namespace Dummy;

class DummyDatabase
{
    private array $tables = [];

    public function insertUser(array $data): int
    {
        $id = count($this->tables['users'] ?? []) + 1;
        $data['id'] = $id;

        $this->tables['users'][$id] = $data;

        return $id;
    }

    public function findUserByUsername(string $username): ?array
    {
        foreach ($this->tables['users'] ?? [] as $row) {
            if ($row['username'] === $username) {
                return $row;
            }
        }
        return null;
    }

    public function allUsers(): array
    {
        return array_values($this->tables['users'] ?? []);
    }
}
