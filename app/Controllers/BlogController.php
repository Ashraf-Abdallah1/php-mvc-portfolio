<?php

namespace App\Controllers;

use App\Models\Blog;
use App\Repositories\BlogRepositoryInterface;
use App\Services\AuthService;
use Framework\Request;
use Framework\Response;
use Framework\ResponseFactory;

class BlogController
{
    private ResponseFactory $responseFactory;
    private BlogRepositoryInterface $blogRepository;

    private AuthService $authService;

    private bool $isAdmin = false;

    public function __construct(
        ResponseFactory         $responseFactory,
        BlogRepositoryInterface $blogRepository,
        AuthService             $authService,
    )
    {
        $this->responseFactory = $responseFactory;
        $this->blogRepository = $blogRepository;
        $this->authService = $authService;
    }

    public function index(): Response
    {
        $this->isAdmin = $this->authService->isAdmin();

        $blogs = $this->blogRepository->all();

        return $this->responseFactory->view('blog/index.html.twig', [
            'isAdmin' => $this->isAdmin,
            'active' => "blog",
        ]);
    }

    public function show(Request $request): Response
    {
        $this->isAdmin = $this->authService->isAdmin();
        $id = (int)$request->get('id');
        $blog = $this->blogRepository->findById($id);
        return $this->responseFactory->view('blog/show.html.twig', [
            'id' => $id,
            'isAdmin' => $this->isAdmin,
            'active' => "blog",
        ]);
    }

    public function create(Request $request): Response
    {
        $blogs = $this->blogRepository->all();
        return $this->responseFactory->view('blog/create.html.twig', [
            'active' => "blog",
        ]);
    }

    public function store(Request $request): Response
    {
        $this->isAdmin = $this->authService->isAdmin();
        $blog = new Blog();
        $blog->setTitle((string)$request->get('title'));
        $blog->setContent((string)$request->get('content'));
        $blog->setDate(time());

        $errors = $this->validateBlog($blog);
        if (!empty($errors)) {
            return $this->responseFactory->view('blog/create.html.twig', [
                'errors' => $errors,
                'oldBlog' => $blog,
                'isAdmin' => $this->isAdmin,
                'active' => "blog",
            ]);
        }

        $this->blogRepository->insert($blog);
        return $this->responseFactory->redirect('/blogs');
    }


    public function edit(Request $request): Response
    {
        $this->isAdmin = $this->authService->isAdmin();
        $id = (int)$request->get('id');
        $blog = $this->blogRepository->findById($id);
        return $this->responseFactory->view('blog/edit.html.twig', [
            'blog' => $blog,
            'isAdmin' => $this->isAdmin,
            'active' => "blog",
        ]);
    }


    public function update(Request $request): Response
    {
        $this->isAdmin = $this->authService->isAdmin();
        $blog = new Blog();
        $blog->setId((int)$request->get('id'));
        $currentBlog = $this->blogRepository->findById($blog->getId());
        $blog->setTitle((string)$request->get('title'));
        $blog->setContent((string)$request->get('content'));
        $blog->setDate(time());
        if ($currentBlog instanceof Blog) {
            if ($currentBlog->getTitle() !== $blog->getTitle()) {
                $errors = $this->validateBlog($blog);
                if (!empty($errors)) {
                    return $this->responseFactory->view('blog/edit.html.twig', [
                        'errors' => $errors,
                        'oldBlog' => $blog,
                        'isAdmin' => $this->isAdmin,
                        'active' => "blog",
                    ]);
                }
            }
        }

        $this->blogRepository->update($blog);
        return $this->responseFactory->redirect('/blogs/' . $blog->getId());
    }

    public function deleteConfirm(Request $request): Response
    {
        $this->isAdmin = $this->authService->isAdmin();
        $id = $request->get('id');
        $blog = $this->blogRepository->findById((int)$id);
        return $this->responseFactory->view("blog/deleteConfirm.html.twig", [
            "blog" => $blog,
            "isAdmin" => $this->isAdmin,
            'active' => "blog",
        ]);
    }

    public function delete(Request $request): Response
    {
        $this->isAdmin = $this->authService->isAdmin();
        $id = (int)$request->get('id');
        $this->blogRepository->delete($id);
        return $this->responseFactory->redirect('/blogs');
    }

    /**
     * @param Blog $blog
     * @return array<string, string>
     */
    private function validateBlog(Blog $blog): array
    {
        $errors = [];
        $blogs = $this->blogRepository->all();
        foreach ($blogs as $dbBlog) {
            if ($blog->getTitle() == $dbBlog->getTitle()) {
                $errors['title'] = "Deze title is al in gebruik. Kies een uniqe title.";
            }
        }

        return $errors;
    }
}
