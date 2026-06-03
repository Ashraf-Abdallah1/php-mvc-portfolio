<?php

namespace Integration;

use App\Controllers\UserController;
use App\Middleware\AuthMiddleware;
use App\Services\AuthService;
use Dummy\DummyDatabase;
use Dummy\DummyUserRepository;
use Framework\Request;
use Framework\Response;
use Framework\ResponseFactory;
use Framework\Session;
use PHPUnit\Framework\TestCase;

class UserFlowTest extends TestCase
{
    private Session $session;
    private UserController $UserController;
    private AuthMiddleware $authMiddleware;
    private DummyDatabase $db;

    private ResponseFactory $responseFactory;

    protected function setUp(): void
    {
        $this->db = new DummyDatabase();


        $this->session = new Session();

        $userRepository = new DummyUserRepository($this->db);

        $authService = new AuthService($userRepository, $this->session);

        $this->responseFactory = new ResponseFactory(false, "app/views");

        $this->UserController = new UserController(
            $this->responseFactory,
            $userRepository,
            $authService
        );

        $this->authMiddleware = new AuthMiddleware(
            $authService,
            $this->responseFactory,
        );
    }

    public function testFullUserFlow(): void
    {
        //Registreren
        $registerRequest = new Request('POST', '/register', [], [
            'name' => 'Ashraf',
            'username' => 'ashraf123',
            'password' => 'Achraf123&'
        ]);

        $registerResponse = $this->UserController->register($registerRequest);

        $this->assertEquals('Location: /', $registerResponse->header);

        $users = $this->db->allUsers();
        $this->assertCount(1, $users);

        $user = $users[0];
        $this->assertEquals('Ashraf', $user['name']);
        $this->assertEquals('user', $user['role']);
        $this->assertTrue(password_verify('Achraf123&', $user['password']));

        // Login
        $loginRequest = new Request('POST', '/login', [], [
            'username' => 'ashraf123',
            'password' => 'Achraf123&'
        ]);

        $loginResponse = $this->UserController->login($loginRequest);

        $this->assertEquals('Location: /', $loginResponse->header);

        // Check session
        $this->assertEquals(1, $this->session->get('user_id'));
        $this->assertEquals('ashraf123', $this->session->get('username'));
        $this->assertEquals('user', $this->session->get('role'));

        //  Auth middleware ingelogd
        $protectedRequest = new Request('GET', '/profile/edit', [], []);

        $middlewareResponse = $this->authMiddleware->handle($protectedRequest, function () {
            return new Response("OK");
        });

        $this->assertSame("OK", $middlewareResponse->body);


        // Log out
        $logoutRequest = new Request('POST', '/logout', [], []);
        $logoutResponse = $this->UserController->logout($logoutRequest);

        $this->assertEquals('Location: /login', $logoutResponse->header);

        $this->assertNull($this->session->get('user_id'));


        // Auth middleware niet ingelogd
        $protectedRequest = new Request('GET', '/profile/edit', [], []);

        $middlewareResponse = $this->authMiddleware->handle($protectedRequest, function () {
            return new Response("Niet ingelogd");
        });

        $this->assertStringContainsString("401", $middlewareResponse->body);
    }
}
