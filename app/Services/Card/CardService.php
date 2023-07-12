<?php

namespace App\Services\Card;

use Illuminate\Support\Collection;
use Carbon\Carbon;
use App\Models\Card;

class CardService
{
    /**
     * Retrieve a Card by its ID.
     *
     * @param int $id The ID of the card to find.
     * @param bool $authUserIsOwner (Optional) If true, checks if the authenticated user is the owner of the card.
     * @param string $guardUserIsOwner (Optional) The guard of the authenticated user.
     *
     * @return Card|null The found Card object if any, otherwise null.
     */
    public function findCard(int $id, bool $authUserIsOwner = false, string $guardUserIsOwner = 'member'): ?Card
    {
        // Build the base query
        $query = Card::where('id', $id);

        // Add the owner constraint if needed
        if ($authUserIsOwner) {
            $query->where('created_by', auth($guardUserIsOwner)->user()->owner_id);
        }

        // Execute the query and return the result
        return $query->first();
    }

    /**
     * Retrieve an active Card by its ID.
     *
     * @param int $id The ID of the card to find.
     * @param bool $authUserIsOwner (Optional) If true, checks if the authenticated user is the owner of the card.
     * @param string $guardUserIsOwner (Optional) The guard of the authenticated user.
     *
     * @return Card|null The found Card object if any, otherwise null.
     */
    public function findActiveCard(int $id, bool $authUserIsOwner = false, string $guardUserIsOwner = 'member'): ?Card
    {
        // Build the base query
        $query = Card::where('id', $id)
            ->where('is_active', true)
            ->whereHas('club', function ($query) {
                $query->where('is_active', true);
            })
            ->whereHas('partner', function ($query) {
                $query->where('is_active', true);
            });

        // Add the owner constraint if needed
        if ($authUserIsOwner) {
            $query->where('created_by', auth($guardUserIsOwner)->user()->owner_id);
        }

        // Execute the query and return the result
        return $query->first();
    }

    /**
     * Retrieve an active Card by its unique identifier.
     * 
     * Note: This function does not check for card issue date or expiration date. 
     * This is because partners and staff should still be able to see the cards in the dashboard 
     * even if they're outside of their issue and expiration dates.
     *
     * @param string $unique_identifier The ID of the card to find.
     *
     * @return Card|null The found Card object if any, otherwise null.
     */
    public function findActiveCardByIdentifier(string $unique_identifier): ?Card
    {
        // Query for an active card with the given unique identifier and an active club
        return Card::where('unique_identifier', $unique_identifier)
            ->where('is_active', true)
            ->whereHas('club', function ($query) {
                $query->where('is_active', true);
            })
            ->whereHas('partner', function ($query) {
                $query->where('is_active', true);
            })
            ->first();
    }

    /**
     * Retrieve all cards associated with a specific partner.
     *
     * @param int $partner_id ID of the partner to get cards from.
     * @param string $orderBy Field name to sort by (default is 'views').
     * @param string $orderByDirection Sorting direction (default is 'desc').
     * @param string|null $where Optional additional condition field (default is 'is_active').
     * @param mixed|null $whereValue Optional value for the additional condition (default is true).
     * @return Collection A collection of Card objects found, empty collection if no cards are found.
     */
    public function findCardsFromPartner(int $partner_id, string $orderBy = 'views', string $orderByDirection = 'desc', ?string $where = 'is_active', mixed $whereValue = true): Collection
    {
        // Construct a query to select cards where the 'created_by' matches the provided partner id.
        // The orderBy method is used to sort the cards based on 'views' (or any other specified field) in descending order (or specified order).
        $query = Card::where('created_by', $partner_id)
                      ->orderBy($orderBy, $orderByDirection);

        // If additional condition is provided, apply it to the query
        if ($where) {
            $query->where($where, $whereValue);
        }

        // Execute the query and return the result as a collection of Card objects.
        // If no matching cards are found, an empty collection is returned.
        return $query->get();
    }

    /**
     * Retrieve all active cards that are visible by default (`cards.is_visible_by_default`).
     *
     * @return Collection The collection of found Card objects if any, otherwise an empty collection.
     */
    public function findActiveCardsVisibleByDefault(): Collection
    {
        // Get the current time in UTC
        $now = Carbon::now('UTC');

        // Build the base query
        $query = Card::where('is_active', true)
            ->where('is_visible_by_default', true)
            ->where('issue_date', '<=', $now)
            ->where('expiration_date', '>', $now)
            ->whereHas('club', function ($query) {
                $query->where('clubs.is_active', true);
            })
            ->whereHas('partner', function ($query) {
                $query->where('partners.is_active', true);
            })
            ->orderBy('issue_date', 'desc');

        // Execute the query and return the results
        return $query->get();
    }

    /**
     * Retrieve all active cards that are visible when a member is logged in (`cards.is_visible_when_logged_in`).
     *
     * @return Collection The collection of found Card objects if any, otherwise an empty collection.
     */
    public function findActiveCardsVisibleWhenLoggedIn(): Collection
    {
        // Get the current time in UTC
        $now = Carbon::now('UTC');

        // Build the base query
        $query = Card::where('is_active', true)
            ->where('is_visible_when_logged_in', true)
            ->where('issue_date', '<=', $now)
            ->where('expiration_date', '>', $now)
            ->whereHas('club', function ($query) {
                $query->where('clubs.is_active', true);
            })
            ->whereHas('partner', function ($query) {
                $query->where('partners.is_active', true);
            })
            ->orderBy('issue_date', 'desc');

        // Execute the query and return the results
        return $query->get();
    }

    /**
     * Retrieve all active cards followed by a member.
     * 
     * @param int $member_id The ID of member.
     *
     * @return Collection The collection of found Card objects if any, otherwise an empty collection.
     */
    public function findActiveCardsFollowedByMember(int $member_id): Collection
    {
        // Get the current time in UTC
        $now = Carbon::now('UTC');

        // Build the query
        $query = Card::where('is_active', true)
            ->where('issue_date', '<=', $now)
            ->where('expiration_date', '>', $now)
            ->whereHas('club', function ($query) {
                $query->where('clubs.is_active', true);
            })
            ->whereHas('partner', function ($query) {
                $query->where('partners.is_active', true);
            })
            ->whereHas('members', function ($query) use ($member_id) {
                $query->where('members.id', $member_id);
            })
            ->orderBy('issue_date', 'desc');

        // Execute the query and return the results
        return $query->get();
    }

    /**
     * Retrieve all active cards where a member has transactions.
     * 
     * @param int $member_id The ID of member.
     *
     * @return Collection The collection of found Card objects if any, otherwise an empty collection.
     */
    public function findActiveCardsWithMemberTransactions(int $member_id): Collection
    {
        // Get the current time in UTC
        $now = Carbon::now('UTC');
    
        // Build the query
        $query = Card::where('is_active', true)
            ->where('issue_date', '<=', $now)
            ->where('expiration_date', '>', $now)
            ->whereHas('club', function ($query) {
                $query->where('clubs.is_active', true);
            })
            ->whereHas('partner', function ($query) {
                $query->where('partners.is_active', true);
            })
            ->whereHas('transactions.member', function ($query) use ($member_id) {
                $query->where('members.id', $member_id);
            })
            ->orderBy('issue_date', 'desc');
    
        // Execute the query and return the results
        return $query->get();
    }

    /**
     * Associate the authenticated member with the provided card.
     * 
     * @param  Card|null  $card
     * @return bool
     */
    public function followCard(Card $card = null): bool
    {
        // Check if card is not null
        if ($card === null) {
            return false;
        }
        
        // Retrieve the authenticated member
        $member = auth('member')->user();

        // Attach the member to the card without detaching existing members
        $card->members()->syncWithoutDetaching([$member->id]);

        return true;
    }

    /**
     * Disassociate the authenticated member from the provided card.
     * 
     * @param  Card|null  $card
     * @return bool
     */
    public function unfollowCard(Card $card = null): bool
    {
        // If no card is provided, return false immediately.
        if ($card === null) {
            return false;
        }
        
        // Get the currently authenticated member.
        $member = auth('member')->user();

        // Disassociate the member from the card.
        $card->members()->detach($member->id);

        return true;
    }
}