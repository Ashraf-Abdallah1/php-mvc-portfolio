<?php

namespace Controllers;

use App\Controllers\ProfileController;
use App\Models\Profile;
use App\Repositories\ProfileRepositoryInterface;
use App\Services\AuthService;
use Framework\Request;
use Framework\Response;
use Framework\ResponseFactory;
use PHPUnit\Framework\Attributes\AllowMockObjectsWithoutExpectations;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

#[AllowMockObjectsWithoutExpectations]
class ProfileControllerTest extends TestCase
{
    private ProfileRepositoryInterface&MockObject $profileRepository;

    private ResponseFactory&MockObject $responseFactory;

    private AuthService&MockObject $auth;

    private ProfileController $controller;

    protected function setUp(): void
    {
        parent::setUp();

        $this->profileRepository = $this->createMock(ProfileRepositoryInterface::class);
        $this->responseFactory = $this->createMock(ResponseFactory::class);
        $this->auth = $this->createMock(AuthService::class);

        $this->controller = new ProfileController(
            $this->responseFactory,
            $this->profileRepository,
            $this->auth
        );
    }

    public function testIndexReturnsViewWithProfileAndIsAdmin(): void
    {
        $profile = new Profile();
        $profile->setName('Ashraf');

        $expectedResponse = $this->createMock(Response::class);

        $this->auth
            ->expects($this->once())
            ->method('isAdmin')
            ->willReturn(true);

        $this->profileRepository
            ->expects($this->once())
            ->method('findByUserId')
            ->with(1)
            ->willReturn($profile);

        $this->responseFactory
            ->expects($this->once())
            ->method('view')
            ->with('profile/index.html.twig', [
                'profile' => $profile,
                'isAdmin' => true
            ])
            ->willReturn($expectedResponse);

        $response = $this->controller->index();

        $this->assertSame($expectedResponse, $response);
    }

    public function testEditReturnsViewWithProfile(): void
    {
        $profile = new Profile();
        $profile->setName('Ashraf');

        $expectedResponse = $this->createMock(Response::class);

        $this->auth
            ->expects($this->once())
            ->method('getUserId')
            ->willReturn(1);

        $this->profileRepository
            ->expects($this->once())
            ->method('findById')
            ->with(1)
            ->willReturn($profile);

        $this->responseFactory
            ->expects($this->once())
            ->method('view')
            ->with('profile/edit.html.twig', [
                'profile' => $profile
            ])
            ->willReturn($expectedResponse);

        $response = $this->controller->edit();

        $this->assertSame($expectedResponse, $response);
    }

    public function testUpdateUpdatesProfileAndRedirects(): void
    {
        $request = new Request('POST', '/profile', [], [
            'name' => 'Ashraf',
            'bio' => 'Ashraf bio',
            'skills' => 'PHP,JS',
            'experience' => '0002@hz.nl',
            'projects' => 'Project A, Project B',
            'contact' => '0002@hz.nl'
        ]);

        $expectedResponse = $this->createMock(Response::class);

        $this->profileRepository
            ->expects($this->once())
            ->method('update')
            ->with($this->callback(function (Profile $profile) {
                return $profile->getName() === 'Ashraf'
                    && $profile->getBio() === 'Ashraf bio'
                    && $profile->getSkills() === 'PHP,JS'
                    && $profile->getExperience() === '0002@hz.nl'
                    && $profile->getProjects() === 'Project A, Project B'
                    && $profile->getContact() === '0002@hz.nl';
            }));

        $this->responseFactory
            ->expects($this->once())
            ->method('redirect')
            ->with('/profile')
            ->willReturn($expectedResponse);

        $response = $this->controller->update($request);

        $this->assertSame($expectedResponse, $response);
    }
}
