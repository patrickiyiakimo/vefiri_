<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Support\Facades\DB;

final class AccountModerator
{
    public function suspend(User $user, User $actor): void
    {
        $this->ensureCanModerate($user, $actor);

        $user->forceFill([
            'suspended_at' => now(),
            'remember_token' => null,
        ])->save();

        $this->purgeSessions($user);
    }

    public function reactivate(User $user, User $actor): void
    {
        $this->ensureCanModerate($user, $actor);

        $user->forceFill([
            'suspended_at' => null,
        ])->save();
    }

    public function delete(User $user, User $actor): void
    {
        $this->ensureCanModerate($user, $actor);

        $user->forceFill([
            'remember_token' => null,
        ])->save();

        $this->purgeSessions($user);
        $user->delete();
    }

    private function ensureCanModerate(User $user, User $actor): void
    {
        if ($user->is($actor)) {
            throw new AuthorizationException('You cannot moderate your own account.');
        }

        if ($user->isAdmin()) {
            throw new AuthorizationException('Admin accounts cannot be moderated here.');
        }
    }

    private function purgeSessions(User $user): void
    {
        DB::table(config('session.table', 'sessions'))
            ->where('user_id', $user->getKey())
            ->delete();
    }
}
