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

        $stmt = $this->db->prepare(
            "SELECT earned_credits, total_credits 
             FROM Dashboards 
             WHERE user_id = 1"
        );
        $stmt->execute();
        $row = $stmt->fetch();

        $dashboard->setEarnedCredits($row->earned_credits);
        $dashboard->setTotalCredits($row->total_credits);

        $stmtGrades = $this->db->prepare(
            "SELECT course, grade 
             FROM Grades 
             WHERE user_id = 1"
        );
        $stmtGrades->execute();
        $grades = $stmtGrades->fetchAll();

        $gradeList = [];
        foreach ($grades as $g) {
            $gradeList[$g->course] = $g->grade;
        }

        $dashboard->setGrades($gradeList);

        return $dashboard;
    }

    public function updateCredits(int $earned, int $total): void
    {
        $stmt = $this->db->prepare(
            "UPDATE Dashboards 
             SET earned_credits = :earned, total_credits = :total 
             WHERE user_id = 1"
        );

        $stmt->execute([
            'earned' => $earned,
            'total' => $total
        ]);
    }

    public function updateGrade(string $course, float $grade): void
    {
        $stmt = $this->db->prepare(
            "UPDATE Grades 
             SET grade = :grade 
             WHERE user_id = 1 AND course = :course"
        );

        $stmt->execute([
            'grade' => $grade,
            'course' => $course
        ]);
    }

    public function addCourse(string $course, float $grade): void
    {
        $stmt = $this->db->prepare(
            "INSERT INTO Grades (user_id, course, grade)
             VALUES (1, :course, :grade)"
        );

        $stmt->execute([
            'course' => $course,
            'grade' => $grade
        ]);
    }

    public function deleteCourse(string $course): void
    {
        $stmt = $this->db->prepare(
            "DELETE FROM Grades 
             WHERE user_id = 1 AND course = :course"
        );

        $stmt->execute([
            'course' => $course
        ]);
    }
}
