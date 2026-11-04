<?php

namespace App\Http\Controllers\Admin;

use App\Enums\UserRole;
use App\Http\Controllers\Concerns\ProvidesStorefrontData;
use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\AccountModerator;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class UserController extends Controller
{
    use ProvidesStorefrontData;

    public function __construct(private readonly AccountModerator $accounts) {}

    public function index(Request $request): View
    {
        $search = trim((string) $request->query('q', ''));
        $role = UserRole::tryFrom((string) $request->query('role', ''));

        $users = User::query()
            ->when($search !== '', function (Builder $query) use ($search): void {
                $query->where(function (Builder $query) use ($search): void {
                    $query->where('name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%");
                });
            })
            ->when($role !== null, fn (Builder $query) => $query->where('role', $role))
            ->latest()
            ->paginate(20)
            ->withQueryString();

        return view('admin.users.index', [
            ...$this->sharedData(),
            'users' => $users,
            'search' => $search,
            'role' => $role,
            'roles' => UserRole::cases(),
        ]);
    }

    public function suspend(Request $request, User $user): RedirectResponse
    {
        $this->accounts->suspend($user, $request->user());

        return back()->with('status', "{$user->name}'s account has been suspended.");
    }

    public function reactivate(Request $request, User $user): RedirectResponse
    {
        $this->accounts->reactivate($user, $request->user());

        return back()->with('status', "{$user->name}'s account has been reactivated.");
    }

    public function destroy(Request $request, User $user): RedirectResponse
    {
        $this->accounts->delete($user, $request->user());

        return back()->with('status', "{$user->name}'s account has been deleted.");
    }
}
