<?php

namespace App\Policies;

use App\Models\Story;
use App\Models\Vendor;
use App\Models\VendorEmployee;

class StoryPolicy
{
    /**
     * Determine if the supplied actor can manage the story lifecycle.
     */
    public function manage($actor, Story $story): bool
    {
        if ($actor instanceof Vendor) {
            return $actor->restaurants()->where('restaurants.id', $story->restaurant_id)->exists();
        }

        if ($actor instanceof VendorEmployee) {
            return (int) $actor->restaurant_id === (int) $story->restaurant_id;
        }

        return false;
    }
}
