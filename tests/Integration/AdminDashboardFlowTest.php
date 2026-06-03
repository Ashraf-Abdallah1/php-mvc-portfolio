<?php

namespace Integration;

use App\Controllers\DashboardController;
use App\Middleware\AdminMiddleware;
use App\Services\AuthService;
use Dummy\DummyDashboardRepository;
use Dummy\DummyDatabase;
use Dummy\DummyUserRepository;
use Framework\Request;
use Framework\Response;
use Framework\ResponseFactory;
use Framework\Session;
use PHPUnit\Framework\TestCase;

class AdminDashboardFlowTest extends TestCase
{
    private Session $session;
    private DashboardController $dashboardController;
    private AdminMiddleware $adminMiddleware;
    private DummyDatabase $db;
    private ResponseFactory $responseFactory;

    protected function setUp(): void
    {
        $this->db = new DummyDatabase();
        $this->session = new Session();

        $userRepository = new DummyUserRepository($this->db);
        $authService = new AuthService($userRepository, $this->session);

        $this->responseFactory = new ResponseFactory(false, "app/views");

        $dashboardRepository = new DummyDashboardRepository();


        $this->dashboardController = new DashboardController(
            $dashboardRepository,
            $this->responseFactory,
            $authService
        );

        $this->adminMiddleware = new AdminMiddleware(
            $authService,
            $this->responseFactory
        );
    }

    public function testDashboardAdminFlow(): void
    {
        $request = new Request('GET', '/dashboard/edit', [], []);


        // ingelogd -> user
        $this->session->set('role', 'user');

        $response = $this->adminMiddleware->handle($request, function () {
            return new Response("Geen admin");
        });

        $this->assertStringContainsString("403", $response->body);


        //ingelogd admin
        $this->session->set('role', 'admin');

        $response = $this->adminMiddleware->handle($request, function ($req) {
            return $this->dashboardController->edit();
        });

        $this->assertStringContainsString("Dashboard", $response->body);
        $this->assertStringContainsString("<form", $response->body);
    }
}
