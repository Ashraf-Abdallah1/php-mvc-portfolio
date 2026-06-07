<?php

namespace App\Controllers;

use App\Models\Blog;
use App\Repositories\BlogRepositoryInterface;
use Framework\Request;
use Framework\Response;
use Framework\ResponseFactory;

class ApiBlogController extends ApiBaseController
{
    private BlogRepositoryInterface $blogRepository;

    public function __construct(ResponseFactory $responseFactory, BlogRepositoryInterface $blogRepository)
    {
        parent::__construct($responseFactory);
        $this->blogRepository = $blogRepository;
    }

    public function blogs(Request $request): Response
    {
        $blogs = $this->blogRepository->all();
        $data = array_map([$this, 'blogToArray'], $blogs);
        return $this->apiJson('blogs', $data, $request->path, total: count($data));
    }

    public function blog(Request $request): Response
    {
        $id = (int)$request->get('id');
        $blog = $this->blogRepository->findById($id);
        if ($blog === null) {
            return $this->apiError('blogs', 'Blog not found', $request->path, 404, '/api/blogs');
        }
        return $this->apiJson('blogs', $this->blogToArray($blog), $request->path, '/api/blogs/');
    }

    /** @return array<string, mixed> */
    private function blogToArray(Blog $blog): array
    {
        return [
            'id' => $blog->getId(),
            'title' => $blog->getTitle(),
            'content' => $blog->getContent(),
            'date' => $blog->getDate(),
        ];
    }
}
