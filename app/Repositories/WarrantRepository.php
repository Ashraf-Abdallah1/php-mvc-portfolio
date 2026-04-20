<?php

namespace App\Repositories;

use App\Models\Warrant;
use App\Repositories\WarrantRepositoryInterface;

class WarrantRepository implements WarrantRepositoryInterface
{
    /**
     * @inheritDoc
     */
    public function all(): array
    {
        // TODO: Implement all() method.
    }

    public function find(int $id): ?Warrant
    {
        // TODO: Implement find() method.
    }

    public function insert(Warrant $warrant): Warrant
    {
        // TODO: Implement insert() method.
    }

    public function update(Warrant $warrant): bool
    {
        // TODO: Implement update() method.
    }

    /**
     * @inheritDoc
     */
    public function agentWarrants(int $agentId): array
    {
        // TODO: Implement agentWarrants() method.
    }
}
