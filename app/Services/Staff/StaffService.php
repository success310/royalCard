<?php

namespace App\Services\Staff;

use App\Models\Staff;

class StaffService
{
    /**
     * Get an active staff by email address.
     *
     * @param  string  $email Email address.
     * @param  bool  $authUserIsOwner Indicates whether the authenticated user has to be the owner.
     * @return Staff|null Staff object if found, otherwise null.
     */
    public function findActiveByEmail(string $email, bool $authUserIsOwner = false): ?Staff
    {
        $query = Staff::query()
            ->whereActive(true)
            ->where('email', $email);

        if ($authUserIsOwner) {
            $query->where('created_by', auth()->user()->owner_id);
        }

        return $query->first();
    }

    /**
     * Insert a new staff.
     *
     * @param  array  $data Staff data.
     * @return Staff The created staff object.
     */
    public function store(array $data): Staff
    {
        return Staff::create($data);
    }
}
