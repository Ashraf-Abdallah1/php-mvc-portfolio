<?php

namespace App\Controllers;

use App\Repositories\BlogRepositoryInterface;
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

        return $this->responseFactory->view('blog/index.html.twig',[
            'blogs' => $blogs
        ]);
    }

}