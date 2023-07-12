<?php

namespace App\Services\Member;

use App\Models\Member;

class MemberService
{
    /**
     * Retrieve an active member by email address.
     *
     * @param  string  $email Email address.
     * @param  bool  $authUserIsOwner Indicates if the authenticated user has to be the owner.
     * @return object|null Member object if found, otherwise null.
     */
    public function findActiveByEmail(string $email, bool $authUserIsOwner = false): ?object
    {
        $query = Member::where('email', $email)
            ->where('is_active', true);

        if ($authUserIsOwner) {
            $query->where('created_by', auth()->user()->owner_id);
        }

        return $query->first();
    }

    /**
     * Retrieve an active member by email address.
     *
     * @param  string  $email Email address.
     * @return object|null Member object if found, otherwise null.
     */
    public function findActiveByIdentifier(string $unique_identifier): ?object
    {
        $query = Member::where('unique_identifier', $unique_identifier)
            ->where('is_active', true);

        return $query->first();
    }

    /**
     * Insert a new member.
     *
     * @param  array  $data Member data.
     * @return object The newly created member object.
     */
    public function store(array $data): object
    {
        return Member::create($data);
    }
}
