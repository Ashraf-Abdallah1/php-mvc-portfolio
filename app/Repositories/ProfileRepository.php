<?php

namespace App\Repositories;

use App\Models\Profile;
use Framework\Database;

class ProfileRepository implements ProfileRepositoryInterface
{
    private Database $db;

    public function __construct(Database $db)
    {
        $this->db = $db;
    }

    public function findByUserId(int $userId): ?Profile
    {
        $row = $this->db->run(
            "SELECT * FROM Profiles WHERE user_id = :uid",
            ['uid' => $userId]
        )->fetch();

        return $this->fromDbRow($row);
    }

    public function update(Profile $profile): void
    {
        $this->db->run(
            "UPDATE Profiles SET 
                name = :name,
                bio = :bio,
                skills = :skills,
                experience = :experience,
                projects = :projects,
                contact = :contact
             WHERE id = 1",
            [
                'name' => $profile->getName(),
                'bio' => $profile->getBio(),
                'skills' => $profile->getSkills(),
                'experience' => $profile->getExperience(),
                'projects' => $profile->getProjects(),
                'contact' => $profile->getContact(),
            ]
        );
    }

    public function findById(int $id): ?Profile
    {
        $stmt = $this->db->run("SELECT * FROM Profiles WHERE id = :id", ['id' => $id])->fetch();
        return $this->fromDbRow($stmt);
    }

    private function fromDbRow($row): ?Profile
    {
        if (!$row) {
            return null;
        }

        $p = new Profile();
        $p->setId($row->id);
        $p->setName($row->name);
        $p->setBio($row->bio);
        $p->setSkills($row->skills);
        $p->setExperience($row->experience);
        $p->setProjects($row->projects);
        $p->setContact($row->contact);

        return $p;
    }
}
