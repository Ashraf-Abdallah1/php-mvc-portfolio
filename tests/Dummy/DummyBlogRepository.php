<?php

namespace Dummy;

use App\Models\Blog;
use App\Repositories\BlogRepositoryInterface;

class DummyBlogRepository implements BlogRepositoryInterface
{
    private array $blogs = [];

    public function all(): array
    {
        return array_values($this->blogs);
    }

    public function findById(int $id): ?Blog
    {
        return $this->blogs[$id] ?? null;
    }

    public function insert(Blog $blog): Blog
    {
        $id = count($this->blogs) + 1;
        $blog->setId($id);
        $this->blogs[$id] = $blog;
        return $blog;
    }

    public function update(Blog $blog): Blog
    {
        $this->blogs[$blog->getId()] = $blog;
        return $blog;
    }

    public function delete(int $id): bool
    {
        unset($this->blogs[$id]);
        return true;
    }
}
