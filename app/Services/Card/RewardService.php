<?php

namespace App\Services\Card;

use App\Models\Reward;

class RewardService
{
    /**
     * Retrieve an active Reward by its ID.
     *
     * @param int $id The ID of the reward to find.
     * @param bool $authUserIsOwner (Optional) If true, checks if the authenticated user is the owner of the reward.
     * @param string $guardUserIsOwner (Optional) The guard of the authenticated user.
     *
     * @return Reward|null The found Reward object if any, otherwise null.
     */
    public function findActiveReward(int $id, bool $authUserIsOwner = false, string $guardUserIsOwner = 'member'): ?Reward
    {
        // Build the base query
        $query = Reward::where('id', $id)->where('is_active', true);

        // Add the owner constraint if needed
        if ($authUserIsOwner) {
            $query->where('created_by', auth($guardUserIsOwner)->user()->owner_id);
        }

        // Execute the query
        $reward = $query->first();

        // If a reward was found, set its images attribute
        if ($reward !== null) {
            $reward->setAttribute('images', $reward->images);
        }

        // Return the result
        return $reward;
    }
}
