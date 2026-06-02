<?php

namespace App\Repositories;

use App\Models\Dashboard;
use Framework\Database;

class DashboardRepository implements DashboardRepositoryInterface
{
    private Database $db;

    public function __construct(Database $db)
    {
        $this->db = $db;
    }

    public function getDashboardData(): Dashboard
    {
        $dashboard = new Dashboard();
        $row = $this->db->run(
            "SELECT earned_credits, total_credits 
             FROM Dashboards 
             WHERE user_id = 1"
        )->fetch();

        $dashboard->setEarnedCredits($row->earned_credits);
        $dashboard->setTotalCredits($row->total_credits);

        $grades = $this->db->run(
            "SELECT course, grade 
             FROM Grades 
             WHERE user_id = 1"
        )->fetchAll();

        $gradeList = [];
        foreach ($grades as $g) {
            $gradeList[$g->course] = $g->grade;
        }

        $dashboard->setGrades($gradeList);

        return $dashboard;
    }

    public function updateCredits(int $earned, int $total): void
    {
        $this->db->run(
            "UPDATE Dashboards 
             SET earned_credits = :earned, total_credits = :total 
             WHERE user_id = 1",
            [
                'earned' => $earned,
                'total' => $total
            ]
        );
    }

    public function updateGrade(string $course, float $grade): void
    {
        $this->db->run(
            "UPDATE Grades 
             SET grade = :grade 
             WHERE user_id = 1 AND course = :course",
            [
                'grade' => $grade,
                'course' => $course
            ]
        );
    }

    public function addCourse(string $course, float $grade): void
    {
        $this->db->run(
            "INSERT INTO Grades (user_id, course, grade)
             VALUES (1, :course, :grade)",
            [
                'course' => $course,
                'grade' => $grade
            ]
        );
    }

    public function deleteCourse(string $course): void
    {
        $this->db->run(
            "DELETE FROM Grades WHERE user_id = 1 AND course = :course",
            ['course' => $course]
        );
    }

}
