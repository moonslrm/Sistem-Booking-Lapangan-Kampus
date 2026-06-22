<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Venue;

class VenuePolicy
{
    public function manage(User $user, Venue $venue): bool
    {
        return $user->hasAnyRole(['admin', 'koorlap']) && $venue->managed_by === $user->id;
    }
}
