<?php

namespace App\Controllers;

use App\Repositories\DashboardRepositoryInterface;
use App\Services\AuthService;
use Framework\Request;
use Framework\ResponseFactory;
use Framework\Response;

class DashboardController
{
    private DashboardRepositoryInterface $dashboardRepository;
    private ResponseFactory $response;

    private AuthService $authService;

    public function __construct(
        DashboardRepositoryInterface $dashboardRepository,
        ResponseFactory $response,
        AuthService $authService,
    ) {
        $this->dashboardRepository = $dashboardRepository;
        $this->response = $response;
        $this->authService = $authService;
    }

    public function index(): Response
    {
        $dashboard = $this->dashboardRepository->getDashboardData();
        $isAdmin = $this->authService->isAdmin();

        return $this->response->view('dashboard/index.html.twig', [
            'dashboard' => $dashboard,
            'isAdmin' => $isAdmin
        ]);
    }

    public function edit(): Response
    {
        if (!$this->authService->isAdmin()) {
            return $this->response->redirect('errors/403');
        }

        $dashboard = $this->dashboardRepository->getDashboardData();

        return $this->response->view('dashboard/edit.html.twig', [
            'dashboard' => $dashboard
        ]);
    }

    public function updateCredits(Request $request): Response
    {
        if (!$this->authService->isAdmin()) {
            return $this->response->redirect('errors/403');
        }

        $earned = (int)$request->get('earned');
        $total = (int)$request->get('total');

        $this->dashboardRepository->updateCredits($earned, $total);

        return $this->response->redirect('/dashboard');
    }

    public function updateGrade(Request $request): Response
    {
        if (!$this->authService->isAdmin()) {
            return $this->response->redirect('errors/403');
        }

        $course = $request->get('course');
        $grade = (float)$request->get('grade');

        $this->dashboardRepository->updateGrade($course, $grade);

        return $this->response->redirect('/dashboard');
    }

    public function addCourse(Request $request): Response
    {
        if (!$this->authService->isAdmin()) {
            return $this->response->redirect('errors/403');
        }

        $course = $request->get('course');
        $grade = (float)$request->get('grade');

        $this->dashboardRepository->addCourse($course, $grade);

        return $this->response->redirect('/dashboard');
    }

    public function deleteCourse(Request $request): Response
    {
        $course = $request->get('course');

        $this->dashboardRepository->deleteCourse($course);

        return $this->response->redirect('/dashboard');
    }
}
