<?php

namespace App\Repositories;

use App\Models\Profile;

interface ProfileRepositoryInterface
{
    public function findByUserId(int $userId): ?Profile;

    public function update(Profile $profile): void;

    public function findById(int $id): ?Profile;


}