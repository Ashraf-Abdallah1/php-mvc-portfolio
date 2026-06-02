<?php

namespace App\Repositories;

use App\Models\Dashboard;

interface DashboardRepositoryInterface
{
    public function getDashboardData(): Dashboard;

    public function updateCredits(int $earned, int $total): void;

    public function updateGrade(string $course, float $grade): void;

    public function addCourse(string $course, float $grade): void;

    public function deleteCourse(string $course): void;
}
