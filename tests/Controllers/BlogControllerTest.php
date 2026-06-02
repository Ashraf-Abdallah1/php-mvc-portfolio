<?php

namespace Controllers;

use App\Controllers\BlogController;
use App\Models\Blog;
use App\Repositories\BlogRepositoryInterface;
use App\Services\AuthService;
use Framework\Request;
use Framework\Response;
use Framework\ResponseFactory;
use PHPUnit\Framework\Attributes\AllowMockObjectsWithoutExpectations;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

#[AllowMockObjectsWithoutExpectations]
class BlogControllerTest extends TestCase
{
    private BlogRepositoryInterface&MockObject $blogRepository;
    private ResponseFactory&MockObject $responseFactory;

    private AuthService&MockObject $authService;

    private BlogController $controller;

    protected function setUp(): void
    {
        parent::setUp();

        $this->blogRepository = $this->createMock(BlogRepositoryInterface::class);
        $this->responseFactory = $this->createMock(ResponseFactory::class);
        $this->authService = $this->createMock(AuthService::class);

        $this->controller = new BlogController(
            $this->responseFactory,
            $this->blogRepository,
            $this->authService
        );
    }

    public function testIndexReturnsViewWithAllBlogs(): void
    {
        $blogs = [
            new Blog(),
            new Blog()
        ];

        $expectedResponse = $this->createMock(Response::class);

        $this->blogRepository
            ->expects($this->once())
            ->method('all')
            ->willReturn($blogs);

        $this->responseFactory
            ->expects($this->once())
            ->method('view')
            ->with('blog/index.html.twig', [
                'blogs' => $blogs,
                'isAdmin' => $this->authService->isAdmin(),
            ])
            ->willReturn($expectedResponse);

        $response = $this->controller->index();

        $this->assertSame($expectedResponse, $response);
    }

    public function testShowReturnsViewWithBlog(): void
    {
        $blog = new Blog();

        $request = new Request('GET', '/blogs/1', [
            'id' => 1
        ], []);

        $expectedResponse = $this->createMock(Response::class);

        $this->blogRepository
            ->expects($this->once())
            ->method('findById')
            ->with(1)
            ->willReturn($blog);

        $this->responseFactory
            ->expects($this->once())
            ->method('view')
            ->with('blog/show.html.twig', [
                'blog' => $blog,
                'isAdmin' => $this->authService->isAdmin(),
            ])
            ->willReturn($expectedResponse);

        $response = $this->controller->show($request);

        $this->assertSame($expectedResponse, $response);
    }

    public function testCreateReturnsCreateView(): void
    {
        $request = new Request('GET', '/blogs/create', [], []);

        $expectedResponse = $this->createMock(Response::class);

        $this->blogRepository
            ->expects($this->once())
            ->method('all')
            ->willReturn([]);

        $this->responseFactory
            ->expects($this->once())
            ->method('view')
            ->with('blog/create.html.twig')
            ->willReturn($expectedResponse);

        $response = $this->controller->create($request);

        $this->assertSame($expectedResponse, $response);
    }

    public function testStoreCreatesBlogAndRedirects(): void
    {
        $request = new Request('POST', '/blogs', [], [
            'title' => 'Test Blog',
            'content' => 'Test Content'
        ]);

        $expectedResponse = $this->createMock(Response::class);

        $this->blogRepository
            ->method('all')
            ->willReturn([]);

        $this->blogRepository
            ->expects($this->once())
            ->method('insert')
            ->with($this->callback(function (Blog $blog) {
                return $blog->getTitle() === 'Test Blog'
                    && $blog->getContent() === 'Test Content'
                    && $blog->getDate() > 0;
            }));

        $this->responseFactory
            ->expects($this->once())
            ->method('redirect')
            ->with('/blogs')
            ->willReturn($expectedResponse);

        $response = $this->controller->store($request);

        $this->assertSame($expectedResponse, $response);
    }

    public function testStoreReturnsErrorsWhenTitleAlreadyExists(): void
    {
        $existingBlog = new Blog();
        $existingBlog->setTitle('Test Blog');

        $request = new Request('POST', '/blogs', [], [
            'title' => 'Test Blog',
            'content' => 'Test Content'
        ]);

        $expectedResponse = $this->createMock(Response::class);

        $this->blogRepository
            ->expects($this->once())
            ->method('all')
            ->willReturn([$existingBlog]);

        $this->responseFactory
            ->expects($this->once())
            ->method('view')
            ->with(
                'blog/create.html.twig',
                $this->callback(function ($data) {
                    return isset($data['errors']['title']);
                })
            )
            ->willReturn($expectedResponse);

        $response = $this->controller->store($request);

        $this->assertSame($expectedResponse, $response);
    }

    public function testEditReturnsViewWithBlog(): void
    {
        $blog = new Blog();

        $request = new Request('GET', '/blogs/edit', [
            'id' => 1
        ], []);

        $expectedResponse = $this->createMock(Response::class);

        $this->blogRepository
            ->expects($this->once())
            ->method('findById')
            ->with(1)
            ->willReturn($blog);

        $this->responseFactory
            ->expects($this->once())
            ->method('view')
            ->with('blog/edit.html.twig', [
                'blog' => $blog,
                'isAdmin' => $this->authService->isAdmin(),
            ])
            ->willReturn($expectedResponse);

        $response = $this->controller->edit($request);

        $this->assertSame($expectedResponse, $response);
    }

    public function testUpdateUpdatesBlogAndRedirects(): void
    {
        $request = new Request('POST', '/blogs/update', [], [
            'id' => 1,
            'title' => 'Updated Blog',
            'content' => 'Updated Content'
        ]);

        $expectedResponse = $this->createMock(Response::class);

        $this->blogRepository
            ->method('all')
            ->willReturn([]);

        $this->blogRepository
            ->expects($this->once())
            ->method('update')
            ->with($this->callback(function (Blog $blog) {
                return $blog->getId() == 1
                    && $blog->getTitle() === 'Updated Blog'
                    && $blog->getContent() === 'Updated Content';
            }));

        $this->responseFactory
            ->expects($this->once())
            ->method('redirect')
            ->with('/blogs/1')
            ->willReturn($expectedResponse);

        $response = $this->controller->update($request);

        $this->assertSame($expectedResponse, $response);
    }

    public function testDeleteConfirmReturnsViewWithBlog(): void
    {
        $blog = new Blog();

        $request = new Request('GET', '/blogs/delete', [
            'id' => 1
        ], []);

        $expectedResponse = $this->createMock(Response::class);

        $this->blogRepository
            ->expects($this->once())
            ->method('findById')
            ->with(1)
            ->willReturn($blog);

        $this->responseFactory
            ->expects($this->once())
            ->method('view')
            ->with('blog/deleteConfirm.html.twig', [
                'blog' => $blog,
                'isAdmin' => $this->authService->isAdmin(),
            ])
            ->willReturn($expectedResponse);

        $response = $this->controller->deleteConfirm($request);

        $this->assertSame($expectedResponse, $response);
    }

    public function testDeleteRemovesBlogAndRedirects(): void
    {
        $request = new Request('POST', '/blogs/delete', [], [
            'id' => 1
        ]);

        $expectedResponse = $this->createMock(Response::class);

        $this->blogRepository
            ->expects($this->once())
            ->method('delete')
            ->with(1);

        $this->responseFactory
            ->expects($this->once())
            ->method('redirect')
            ->with('/blogs')
            ->willReturn($expectedResponse);

        $response = $this->controller->delete($request);

        $this->assertSame($expectedResponse, $response);
    }
}