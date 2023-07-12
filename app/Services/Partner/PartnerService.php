<?php

namespace App\Services\Partner;

use App\Models\Partner;

class PartnerService
{
    /**
     * Get an active partner by email address.
     *
     * @param  string  $email Email address.
     * @param  bool  $authUserIsOwner Indicates whether the authenticated user has to be the owner.
     * @return Partner|null Partner object if found, otherwise null.
     */
    public function findActiveByEmail(string $email, bool $authUserIsOwner = false): ?Partner
    {
        $query = Partner::query()
            ->whereActive(true)
            ->where('email', $email);

        if ($authUserIsOwner) {
            $query->where('created_by', auth()->user()->owner_id);
        }

        return $query->first();
    }

    /**
     * Insert a new partner.
     *
     * @param  array  $data Partner data.
     * @return Partner The created partner object.
     */
    public function store(array $data): Partner
    {
        return Partner::create($data);
    }
}
