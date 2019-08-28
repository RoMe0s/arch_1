<?php

namespace App\Policies;

use Game\Infrastructure\Persistance\Eloquent\Game;
use Game\Infrastructure\Persistance\Eloquent\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class GamePolicy
{
    use HandlesAuthorization;

    public function show(User $user, Game $game): bool
    {
        return $game->owner_id === $user->id
            || $game->competitor_id === $user->id
            || is_null($game->competitor_id);
    }

    public function participate(User $user, Game $game): bool
    {
        return $game->owner_id === $user->id || $game->competitor_id === $user->id;
    }
}
