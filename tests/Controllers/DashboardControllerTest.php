<?php

namespace Controllers;

use App\Controllers\DashboardController;
use App\Models\Dashboard;
use App\Repositories\DashboardRepositoryInterface;
use App\Services\AuthService;
use Framework\Request;
use Framework\Response;
use Framework\ResponseFactory;
use PHPUnit\Framework\Attributes\AllowMockObjectsWithoutExpectations;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

#[AllowMockObjectsWithoutExpectations]
class DashboardControllerTest extends TestCase
{
    private DashboardRepositoryInterface&MockObject $dashboardRepository;

    private ResponseFactory&MockObject $responseFactory;

    private AuthService&MockObject $authService;

    private DashboardController $dashboardController;

    protected function setUp(): void
    {
        parent::setUp();

        $this->dashboardRepository = $this->createMock(DashboardRepositoryInterface::class);
        $this->responseFactory = $this->createMock(ResponseFactory::class);
        $this->authService = $this->createMock(AuthService::class);

        $this->dashboardController = new DashboardController(
            $this->dashboardRepository,
            $this->responseFactory,
            $this->authService
        );
    }

    public function testIndexReturnsViewWithDashboard(): void
    {
        $dashboard = $this->createMock(Dashboard::class);

        $expectedResponse = $this->createMock(Response::class);

        $this->dashboardRepository
            ->expects($this->once())
            ->method('getDashboardData')
            ->willReturn($dashboard);

        $this->authService
            ->expects($this->once())
            ->method('isAdmin')
            ->willReturn(true);

        $this->responseFactory
            ->expects($this->once())
            ->method('view')
            ->with('dashboard/index.html.twig', [
                'dashboard' => $dashboard,
                'isAdmin' => true,
                'active' => "dashboard",
            ])
            ->willReturn($expectedResponse);

        $response = $this->dashboardController->index();

        $this->assertSame($expectedResponse, $response);
    }

    public function testEditRedirectsWhenNotAdmin(): void
    {
        $expectedResponse = $this->createMock(Response::class);

        $this->authService
            ->expects($this->once())
            ->method('isAdmin')
            ->willReturn(false);

        $this->responseFactory
            ->expects($this->once())
            ->method('redirect')
            ->with('errors/403')
            ->willReturn($expectedResponse);

        $response = $this->dashboardController->edit();

        $this->assertSame($expectedResponse, $response);
    }

    public function testEditReturnsViewWhenAdmin(): void
    {
        $dashboard = $this->createMock(\App\Models\Dashboard::class);
        $expectedResponse = $this->createMock(Response::class);

        $this->authService
            ->expects($this->once())
            ->method('isAdmin')
            ->willReturn(true);

        $this->dashboardRepository
            ->expects($this->once())
            ->method('getDashboardData')
            ->willReturn($dashboard);

        $this->responseFactory
            ->expects($this->once())
            ->method('view')
            ->with('dashboard/edit.html.twig', [
                'dashboard' => $dashboard,
                'active' => "dashboard",
            ])
            ->willReturn($expectedResponse);

        $response = $this->dashboardController->edit();

        $this->assertSame($expectedResponse, $response);
    }

    public function testUpdateCreditsRedirectsTo403WhenNotAdmin(): void
    {
        $request = new Request('POST', '/dashboard/update-credits', [], [
            'earned' => '5',
            'total' => '10'
        ]);

        $expectedResponse = $this->createMock(Response::class);

        $this->authService
            ->expects($this->once())
            ->method('isAdmin')
            ->willReturn(false);

        $this->responseFactory
            ->expects($this->once())
            ->method('redirect')
            ->with('errors/403')
            ->willReturn($expectedResponse);

        $response = $this->dashboardController->updateCredits($request);

        $this->assertSame($expectedResponse, $response);
    }

    public function testUpdateCreditsCallsRepositoryAndRedirectsWhenAdmin(): void
    {
        $request = new Request('POST', '/dashboard/update-credits', [], [
            'earned' => '7',
            'total' => '20'
        ]);

        $expectedResponse = $this->createMock(Response::class);

        $this->authService
            ->expects($this->once())
            ->method('isAdmin')
            ->willReturn(true);

        $this->dashboardRepository
            ->expects($this->once())
            ->method('updateCredits')
            ->with(7, 20);

        $this->responseFactory
            ->expects($this->once())
            ->method('redirect')
            ->with('/dashboard')
            ->willReturn($expectedResponse);

        $response = $this->dashboardController->updateCredits($request);

        $this->assertSame($expectedResponse, $response);
    }

    public function testUpdateGradeRedirectsTo403WhenNotAdmin(): void
    {
        $request = new Request('POST', '/dashboard/update-grade', [], [
            'course' => 'Rekenen',
            'grade' => '8.5'
        ]);

        $expectedResponse = $this->createMock(Response::class);

        $this->authService
            ->expects($this->once())
            ->method('isAdmin')
            ->willReturn(false);

        $this->responseFactory
            ->expects($this->once())
            ->method('redirect')
            ->with('errors/403')
            ->willReturn($expectedResponse);

        $response = $this->dashboardController->updateGrade($request);

        $this->assertSame($expectedResponse, $response);
    }

    public function testUpdateGradeCallsRepositoryAndRedirectsWhenAdmin(): void
    {
        $request = new Request('POST', '/dashboard/update-grade', [], [
            'course' => 'Bio',
            'grade' => '9.25'
        ]);

        $expectedResponse = $this->createMock(Response::class);

        $this->authService
            ->expects($this->once())
            ->method('isAdmin')
            ->willReturn(true);

        $this->dashboardRepository
            ->expects($this->once())
            ->method('updateGrade')
            ->with('Bio', 9.25);

        $this->responseFactory
            ->expects($this->once())
            ->method('redirect')
            ->with('/dashboard')
            ->willReturn($expectedResponse);

        $response = $this->dashboardController->updateGrade($request);

        $this->assertSame($expectedResponse, $response);
    }

    public function testAddCourseRedirectsTo403WhenNotAdmin(): void
    {
        $request = new Request('POST', '/dashboard/add-course', [], [
            'course' => 'Bio',
            'grade' => '7.0'
        ]);

        $expectedResponse = $this->createMock(Response::class);

        $this->authService
            ->expects($this->once())
            ->method('isAdmin')
            ->willReturn(false);

        $this->responseFactory
            ->expects($this->once())
            ->method('redirect')
            ->with('errors/403')
            ->willReturn($expectedResponse);

        $response = $this->dashboardController->addCourse($request);

        $this->assertSame($expectedResponse, $response);
    }

    public function testAddCourseCallsRepositoryAndRedirectsWhenAdmin(): void
    {
        $request = new Request('POST', '/dashboard/add-course', [], [
            'course' => 'Sport',
            'grade' => '8.0'
        ]);

        $expectedResponse = $this->createMock(Response::class);

        $this->authService
            ->expects($this->once())
            ->method('isAdmin')
            ->willReturn(true);

        $this->dashboardRepository
            ->expects($this->once())
            ->method('addCourse')
            ->with('Sport', 8.0);

        $this->responseFactory
            ->expects($this->once())
            ->method('redirect')
            ->with('/dashboard')
            ->willReturn($expectedResponse);

        $response = $this->dashboardController->addCourse($request);

        $this->assertSame($expectedResponse, $response);
    }

    public function testDeleteCourseCallsRepositoryAndRedirects(): void
    {
        $request = new Request('POST', '/dashboard/delete-course', [], [
            'course' => 'Bio'
        ]);

        $expectedResponse = $this->createMock(Response::class);

        $this->dashboardRepository
            ->expects($this->once())
            ->method('deleteCourse')
            ->with('Bio');

        $this->responseFactory
            ->expects($this->once())
            ->method('redirect')
            ->with('/dashboard')
            ->willReturn($expectedResponse);

        $response = $this->dashboardController->deleteCourse($request);

        $this->assertSame($expectedResponse, $response);
    }
}
