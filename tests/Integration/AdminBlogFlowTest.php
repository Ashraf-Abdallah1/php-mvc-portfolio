<?php

namespace Integration;

use App\Controllers\BlogController;
use App\Middleware\AdminMiddleware;
use App\Models\Blog;
use App\Services\AuthService;
use Dummy\DummyBlogRepository;
use Dummy\DummyDatabase;
use Dummy\DummyUserRepository;
use Framework\Request;
use Framework\Response;
use Framework\ResponseFactory;
use Framework\Session;
use PHPUnit\Framework\TestCase;

class AdminBlogFlowTest extends TestCase
{
    private Session $session;
    private BlogController $blogController;
    private AdminMiddleware $adminMiddleware;
    private DummyBlogRepository $blogRepository;

    protected function setUp(): void
    {
        $db = new DummyDatabase();
        $this->session = new Session();

        $userRepository = new DummyUserRepository($db);
        $authService = new AuthService($userRepository, $this->session);

        $responseFactory = new ResponseFactory(false, "app/views");

        $this->blogRepository = new DummyBlogRepository();

        $this->blogController = new BlogController(
            $responseFactory,
            $this->blogRepository,
            $authService
        );

        $this->adminMiddleware = new AdminMiddleware(
            $authService,
            $responseFactory
        );
    }

    public function testAdminBlogManagementFlow(): void
    {
        //  niet ingelogd
        $request = new Request('GET', '/blogs/1/edit', [], []);

        $response = $this->adminMiddleware->handle($request, function () {
            return new Response("Unauthorized");
        });

        $this->assertStringContainsString("403", $response->body);


        // ingelogd als user en niet admin
        $this->session->set('role', 'user');

        $response = $this->adminMiddleware->handle($request, function () {
            return new Response("Geen admin");
        });

        $this->assertStringContainsString("403", $response->body);


        // ingelogd amin
        $this->session->set('role', 'admin');

        $blog = new Blog();
        $blog->setTitle("Test Blog");
        $blog->setContent("Content");
        $blog->setDate(time());
        $this->blogRepository->insert($blog);

        $response = $this->adminMiddleware->handle($request, function ($req) {
            return $this->blogController->edit($req);
        });

        $this->assertStringContainsString("Blog Bewerken", $response->body);
    }
}
