<?php

namespace App\Http\Controllers\Admin;

use App\Enums\UserRole;
use App\Http\Controllers\Concerns\ProvidesStorefrontData;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

class UserController extends Controller
{
    use ProvidesStorefrontData;

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
}
