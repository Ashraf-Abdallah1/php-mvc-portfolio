<?php

namespace App\Repositories;

use App\Models\Blog;

interface BlogRepositoryInterface
{
    /**
     * @return array<Blog>
     */
    public function all(): array;

    public function findById(int $id): ?Blog;

    public function insert(Blog $blog): Blog;

    public function update(Blog $blog): Blog;

    public function delete(int $id): bool;
}
