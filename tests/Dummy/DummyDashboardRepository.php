<?php

namespace Dummy;

use App\Models\Dashboard;
use App\Repositories\DashboardRepositoryInterface;

class DummyDashboardRepository implements DashboardRepositoryInterface
{
    private Dashboard $dashboard;

    public function __construct()
    {
        $this->dashboard = new Dashboard();
        $this->dashboard->setEarnedCredits(30);
        $this->dashboard->setTotalCredits(60);
        $this->dashboard->setGrades([
            'PHP' => 8.5,
            'JS' => 7.0
        ]);
    }

    public function getDashboardData(): Dashboard
    {
        return $this->dashboard;
    }

    public function updateCredits(int $earned, int $total): void
    {
        $this->dashboard->setEarnedCredits($earned);
        $this->dashboard->setTotalCredits($total);
    }

    public function updateGrade(string $course, float $grade): void
    {
        $grades = $this->dashboard->getGrades();
        $grades[$course] = $grade;
        $this->dashboard->setGrades($grades);
    }

    public function addCourse(string $course, float $grade): void
    {
        $grades = $this->dashboard->getGrades();
        $grades[$course] = $grade;
        $this->dashboard->setGrades($grades);
    }

    public function deleteCourse(string $course): void
    {
        $grades = $this->dashboard->getGrades();
        unset($grades[$course]);
        $this->dashboard->setGrades($grades);
    }
}
