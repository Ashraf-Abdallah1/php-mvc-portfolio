<?php

namespace App\Controllers;

use App\Repositories\BlogRepositoryInterface;
use Framework\Request;
use Framework\Response;
use Framework\ResponseFactory;

class BlogController
{
    private ResponseFactory $responseFactory;
    private BlogRepositoryInterface $blogRepository;

    public function __construct(ResponseFactory $responseFactory, BlogRepositoryInterface $blogRepository)
    {
        $this->responseFactory = $responseFactory;
        $this->blogRepository = $blogRepository;
    }

    public function index(): Response
    {
        $blogs = $this->blogRepository->all();

        return $this->responseFactory->view('blog/index.html.twig', [
            'blogs' => $blogs
        ]);
    }

    public function show(Request $request): Response
    {
        $id = (int)$request->get('id');
        $blog = $this->blogRepository->findById($id);
        return $this->responseFactory->view('blog/show.html.twig', [
            'blog' => $blog
        ]);
    }

    public function create(Request $request): Response{
        return $this->responseFactory->view('blog/create.html.twig');
    }

}