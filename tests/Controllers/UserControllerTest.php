<?php

namespace Controllers;

use App\Controllers\UserController;
use App\Models\User;
use App\Repositories\UserRepositoryInterface;
use App\Services\AuthService;
use Framework\Request;
use Framework\Response;
use Framework\ResponseFactory;
use PHPUnit\Framework\Attributes\AllowMockObjectsWithoutExpectations;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

#[AllowMockObjectsWithoutExpectations]
class UserControllerTest extends TestCase
{
    private UserRepositoryInterface&MockObject $userRepository;

    private ResponseFactory&MockObject $responseFactory;

    private AuthService&MockObject $authService;

    private UserController $userController;

    protected function setUp(): void
    {
        parent::setUp();

        $this->userRepository = $this->createMock(UserRepositoryInterface::class);
        $this->responseFactory = $this->createMock(ResponseFactory::class);
        $this->authService = $this->createMock(AuthService::class);

        $this->userController = new UserController(
            $this->responseFactory,
            $this->userRepository,
            $this->authService
        );
    }

    public function testRegisterFormReturnsView(): void
    {
        $expectedResponse = $this->createMock(Response::class);

        $this->responseFactory
            ->expects($this->once())
            ->method('view')
            ->with('user/register.html.twig')
            ->willReturn($expectedResponse);

        $response = $this->userController->registerForm(new Request('GET', '/register', [], []));

        $this->assertSame($expectedResponse, $response);
    }

    public function testRegisterCreatesUserAndRedirects(): void
    {
        $request = new Request('POST', '/register', [], [
            'name' => 'Ashraf',
            'username' => 'ashraf123',
            'password' => 'Achraf123&'
        ]);

        $expectedResponse = $this->createMock(Response::class);

        $this->userRepository
            ->expects($this->once())
            ->method('all')
            ->willReturn([]);

        $this->authService
            ->expects($this->once())
            ->method('register')
            ->with($this->callback(function (User $user) {
                return $user->getName() === 'Ashraf'
                    && $user->getUsername() === 'ashraf123';
            }), 'Achraf123&');

        $this->responseFactory
            ->expects($this->once())
            ->method('redirect')
            ->with('/')
            ->willReturn($expectedResponse);

        $response = $this->userController->register($request);

        $this->assertSame($expectedResponse, $response);
    }

    public function testRegisterReturnsErrorsWhenValidationFails(): void
    {
        $request = new Request('POST', '/register', [], [
            'name' => 'Ashraf',
            'username' => '',
            'password' => 'short'
        ]);

        $expectedResponse = $this->createMock(Response::class);

        $this->userRepository
            ->expects($this->once())
            ->method('all')
            ->willReturn([]);

        $this->responseFactory
            ->expects($this->once())
            ->method('view')
            ->with(
                'user/register.html.twig',
                $this->callback(function ($data) {
                    return isset($data['errors']) && isset($data['old']['username']);
                })
            )
            ->willReturn($expectedResponse);

        $response = $this->userController->register($request);

        $this->assertSame($expectedResponse, $response);
    }

    public function testLoginFormReturnsView(): void
    {
        $expectedResponse = $this->createMock(Response::class);

        $this->responseFactory
            ->expects($this->once())
            ->method('view')
            ->with('user/login.html.twig')
            ->willReturn($expectedResponse);

        $response = $this->userController->loginForm(new Request('GET', '/login', [], []));

        $this->assertSame($expectedResponse, $response);
    }

    public function testLoginRedirectsOnSuccess(): void
    {
        $request = new Request('POST', '/login', [], [
            'username' => 'ashraf123',
            'password' => 'Achraf123&'
        ]);

        $expectedResponse = $this->createMock(Response::class);
        $user = new User();

        $this->authService
            ->expects($this->once())
            ->method('login')
            ->with('ashraf123', 'Achraf123&')
            ->willReturn($user);

        $this->responseFactory
            ->expects($this->once())
            ->method('redirect')
            ->with('/')
            ->willReturn($expectedResponse);

        $response = $this->userController->login($request);

        $this->assertSame($expectedResponse, $response);
    }

    public function testLoginReturnsViewOnFailure(): void
    {
        $request = new Request('POST', '/login', [], [
            'username' => 'ashraf123',
            'password' => 'wrongpass'
        ]);

        $expectedResponse = $this->createMock(Response::class);
        $errors = ['credentials' => 'Invalid username or password'];

        $this->authService
            ->expects($this->once())
            ->method('login')
            ->with('ashraf123', 'wrongpass')
            ->willReturn($errors);

        $this->responseFactory
            ->expects($this->once())
            ->method('view')
            ->with(
                'user/login.html.twig',
                $this->callback(function ($data) use ($request) {
                    return isset($data['errors']) && $data['errors'] === ['credentials' => 'Invalid username or password']
                        && isset($data['old']) && $data['old']['username'] === 'ashraf123';
                })
            )
            ->willReturn($expectedResponse);

        $response = $this->userController->login($request);

        $this->assertSame($expectedResponse, $response);
    }

    public function testLogoutCallsAuthAndRedirects(): void
    {
        $request = new Request('POST', '/logout', [], []);

        $expectedResponse = $this->createMock(Response::class);

        $this->authService
            ->expects($this->once())
            ->method('logout');

        $this->responseFactory
            ->expects($this->once())
            ->method('redirect')
            ->with('/login')
            ->willReturn($expectedResponse);

        $response = $this->userController->logout($request);

        $this->assertSame($expectedResponse, $response);
    }
}
