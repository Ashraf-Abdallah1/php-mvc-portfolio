<?php

namespace App\Repositories;

use App\Models\Blog;
use App\Repositories\BlogRepositoryInterface;
use Framework\Database;

class BlogRepository implements BlogRepositoryInterface
{
    private Database $database;

    public function __construct(Database $database)
    {
        $this->database = $database;
    }

    /**
     * @inheritDoc
     */
    public function all(): array
    {
        $stmt = $this->database->run("SELECT * FROM Blogs")->fetchAll();
        $blogs = [];
        foreach ($stmt as $blog) {
            $rowBlog = $this->fromDbRow($blog);
            $blogs[] = $rowBlog;
        }
        return $blogs;
    }

    public function findById(int $id): ?Blog
    {
        return null;
    }

    public function insert(Blog $blog): Blog
    {
        return $blog;
    }

    public function update(Blog $blog): Blog
    {
        return $blog;
    }

    public function delete(int $id): bool
    {
        return false;
    }

    public function fromDbRow(mixed $row): Blog
    {
        $blog = new Blog();
        $blog->setTitle($row->title);
        $blog->setContent($row->content);
        $blog->setDate($row->date);
        $blog->setId($row->id);
        return $blog;
    }
}