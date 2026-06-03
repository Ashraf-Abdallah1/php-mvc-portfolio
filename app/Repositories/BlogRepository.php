<?php

namespace App\Repositories;

use App\Models\Blog;
use Framework\Database;
use RuntimeException;

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
        $blogs = [];

        $stmt = $this->database->prepare("SELECT * FROM Blogs");
        $stmt->execute();
        $rows = $stmt->fetchAll();

        foreach ($rows as $blog) {
            $rowBlog = $this->fromDbRow($blog);
            $blogs[] = $rowBlog;
        }

        return $blogs;
    }

    public function findById(int $id): ?Blog
    {
        $stmt = $this->database->prepare(
            "SELECT * FROM Blogs WHERE id = :id"
        );

        $stmt->execute(['id' => $id]);

        $row = $stmt->fetch();

        return $this->fromDbRow($row);
    }

    public function insert(Blog $blog): Blog
    {
        $stmt = $this->database->prepare(
            "INSERT INTO Blogs (title, content, created_date)
             VALUES (:title, :content, :created_date)"
        );

        $stmt->execute([
            "title" => $blog->getTitle(),
            "content" => $blog->getContent(),
            "created_date" => $blog->getDate()
        ]);

        $blog->setId($this->database->getLastID());
        return $blog;
    }

    public function update(Blog $blog): Blog
    {
        $stmt = $this->database->prepare(
            "UPDATE Blogs SET
                title = :title,
                content = :content,
                created_date = :created_date
             WHERE id = :id"
        );

        $stmt->execute([
            "title" => $blog->getTitle(),
            "id" => $blog->getId(),
            "content" => $blog->getContent(),
            "created_date" => $blog->getDate()
        ]);

        return $blog;
    }

    public function delete(int $id): bool
    {
        $stmt = $this->database->prepare(
            "DELETE FROM Blogs WHERE id = :id"
        );

        $stmt->execute(['id' => $id]);

        return $stmt->rowCount() > 0;
    }

    public function fromDbRow(mixed $row): Blog
    {
        if ($row === null) {
            throw new RuntimeException("Invalid DB row for Blog");
        }

        $blog = new Blog();
        $blog->setTitle($row->title);
        $blog->setContent($row->content);
        $blog->setDate($row->created_date);
        $blog->setId($row->id);

        return $blog;
    }
}
