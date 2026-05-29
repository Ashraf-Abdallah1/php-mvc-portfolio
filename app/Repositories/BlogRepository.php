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
        $stmt = $this->database->run("SELECT * FROM Blogs WHERE id = :id", ['id' => $id])->fetch();
        return $this->fromDbRow($stmt);
    }

    public function insert(Blog $blog): Blog
    {
        $stmt = $this->database->run("INSERT INTO Blogs (title, content, created_date) VALUES (:title, :content, :created_date)", [
            "title" => $blog->getTitle(),
            "content" => $blog->getContent(),
            "created_date" => $blog->getDate()
        ]);
        $blog->setId($this->database->getLastID());
        return $blog;
    }

    public function update(Blog $blog): Blog
    {
        $stmt = $this->database->run("update Blogs set title =:title, content=:content, created_date=:created_date  where id = :id", [
            "title" => $blog->getTitle(),
            "id" => $blog->getId(),
            "content" => $blog->getContent(),
            "created_date" => $blog->getDate()
        ]);
        return $blog;
    }

    public function delete(int $id): bool
    {
        $stmt = $this->database->run("delete from Blogs where id = :id", ['id' => $id]);

        return $stmt->rowCount() > 0;
    }

    public function fromDbRow(mixed $row): Blog
    {
        $blog = new Blog();
        $blog->setTitle($row->title);
        $blog->setContent($row->content);
        $blog->setDate($row->created_date);
        $blog->setId($row->id);
        return $blog;
    }
}
