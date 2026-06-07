<?php

namespace App\Models;

class Dashboard
{
    private int $earnedCredits;
    private int $totalCredits;

    // hier een probleem return type TODO
    /**
     * @var array <string, int>
     */
    private array $grades;

    public function getEarnedCredits(): int
    {
        return $this->earnedCredits;
    }

    public function setEarnedCredits(int $c): void
    {
        $this->earnedCredits = $c;
    }

    public function getTotalCredits(): int
    {
        return $this->totalCredits;
    }

    public function setTotalCredits(int $c): void
    {
        $this->totalCredits = $c;
    }

    /**
     * @return array<string, int>
     */
    public function getGrades(): array
    {
        return $this->grades;
    }


    /**
     * @param array<string, int> $grades
     * @return void
     */
    public function setGrades(array $grades): void
    {
        $this->grades = $grades;
    }

    public function getProgressPercentage(): float
    {
        return ($this->earnedCredits / $this->totalCredits) * 100;
    }
}
