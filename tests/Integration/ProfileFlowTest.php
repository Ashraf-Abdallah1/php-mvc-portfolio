<?php

namespace Integration;

use App\Controllers\ProfileController;
use App\Middleware\AdminMiddleware;
use App\Services\AuthService;
use Dummy\DummyDatabase;
use Dummy\DummyUserRepository;
use Dummy\DummyProfileRepository;
use Framework\Request;
use Framework\Response;
use Framework\ResponseFactory;
use Framework\Session;
use PHPUnit\Framework\TestCase;

class ProfileFlowTest extends TestCase
{
    private Session $session;
    private ProfileController $profileController;
    private AdminMiddleware $adminMiddleware;
    private DummyProfileRepository $profileRepository;

    protected function setUp(): void
    {
        $this->session = new Session();
        $db = new DummyDatabase();

        $userRepository = new DummyUserRepository($db);
        $auth = new AuthService($userRepository, $this->session);

        $responseFactory = new ResponseFactory(false, "app/views");

        $this->profileRepository = new DummyProfileRepository();

        $this->profileController = new ProfileController(
            $responseFactory,
            $this->profileRepository,
            $auth
        );

        $this->adminMiddleware = new AdminMiddleware(
            $auth,
            $responseFactory
        );
    }

    public function testProfileEditRequiresAdmin(): void
    {
        $request = new Request('GET', '/profile/edit', [], []);

        //  ingelogd  user
        $this->session->set('role', 'user');
        $response = $this->adminMiddleware->handle($request, fn() => new Response("Niet ingelogd"));
        $this->assertStringContainsString("403", $response->body);

        // Admin  mag door
        $this->session->set('role', 'admin');
        $response = $this->adminMiddleware->handle($request, fn() => $this->profileController->edit());
        $this->assertStringContainsString("form", $response->body);
    }

    public function testProfileUpdate(): void
    {
        $this->session->set('role', 'admin');

        $request = new Request(
            'POST',
            '/profile/1',
            [],
            [
                'name' => 'Ashraf',
                'bio' => 'Bio',
                'skills' => 'PHP, JS',
                'experience' => '1 year',
                'projects' => 'Project education game',
                'contact' => '0002abda@hz.nl'
            ]
        );

        $response = $this->profileController->update($request);

        $this->assertEquals('Location: /profile', $response->header);

        $this->assertEquals('Ashraf', $this->profileRepository->savedProfile->getName());
        $this->assertEquals('Bio', $this->profileRepository->savedProfile->getBio());
    }

    public function testProfileIndexAccessibleForAll(): void
    {
        $response = $this->profileController->index();

        $this->assertStringContainsString("Test User", $response->body);
    }
}
