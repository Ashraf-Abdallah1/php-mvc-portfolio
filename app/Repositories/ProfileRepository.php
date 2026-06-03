<?php

namespace App\Repositories;

use App\Models\Profile;
use Framework\Database;

class ProfileRepository implements ProfileRepositoryInterface
{
    private Database $database;

    public function __construct(Database $database)
    {
        $this->database = $database;
    }

    public function findByUserId(int $userId): ?Profile
    {
        $stmt = $this->database->prepare(
            "SELECT * FROM Profiles WHERE user_id = :uid"
        );
        $stmt->execute(['uid' => $userId]);

        $row = $stmt->fetch();

        return $this->fromDbRow($row);
    }

    public function update(Profile $profile): void
    {
        $stmt = $this->database->prepare(
            "UPDATE Profiles SET 
                name = :name,
                bio = :bio,
                skills = :skills,
                experience = :experience,
                projects = :projects,
                contact = :contact
             WHERE id = 1"
        );

        $stmt->execute([
            'name' => $profile->getName(),
            'bio' => $profile->getBio(),
            'skills' => $profile->getSkills(),
            'experience' => $profile->getExperience(),
            'projects' => $profile->getProjects(),
            'contact' => $profile->getContact(),
        ]);
    }

    public function findById(int $id): ?Profile
    {
        $stmt = $this->database->prepare(
            "SELECT * FROM Profiles WHERE id = :id"
        );
        $stmt->execute(['id' => $id]);

        $row = $stmt->fetch();

        return $this->fromDbRow($row);
    }

    private function fromDbRow(mixed $row): ?Profile
    {
        if (!$row) {
            return null;
        }

        $profile = new Profile();
        $profile->setId($row->id);
        $profile->setName($row->name);
        $profile->setBio($row->bio);
        $profile->setSkills($row->skills);
        $profile->setExperience($row->experience);
        $profile->setProjects($row->projects);
        $profile->setContact($row->contact);

        return $profile;
    }
}
