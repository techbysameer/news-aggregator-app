<?php

namespace App\Repositories;

use App\Models\UserPreference;

class UserPreferenceRepository
{
    public function setUserPreferences($userId, $data)
    {
        return UserPreference::updateOrCreate(
            ['user_id' => $userId],
            $data
        );
    }

    public function getUserPreferences($userId)
    {
        return UserPreference::where('user_id', $userId)->first();
    }
}
