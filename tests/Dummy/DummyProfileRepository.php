<?php

namespace Dummy;

use App\Models\Profile;
use App\Repositories\ProfileRepositoryInterface;

class DummyProfileRepository implements ProfileRepositoryInterface
{
    public Profile $savedProfile;

    public function findByUserId(int $userId): ?Profile
    {
        $p = new Profile();
        $p->setId(1);
        $p->setName("Test User");
        $p->setBio("Bio");
        $p->setSkills("PHP");
        $p->setExperience("None");
        $p->setProjects("None");
        $p->setContact("0002abda@hz.nl");
        return $p;
    }

    public function findById(int $id): ?Profile
    {
        return $this->findByUserId(1);
    }

    public function update(Profile $profile): void
    {
        $this->savedProfile = $profile;
    }
}
