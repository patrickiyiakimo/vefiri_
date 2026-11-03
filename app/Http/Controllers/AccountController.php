<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Concerns\ProvidesStorefrontData;
use App\Http\Requests\UpdatePasswordRequest;
use App\Http\Requests\UpdateProfileRequest;
use App\Services\CartService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class AccountController extends Controller
{
    use ProvidesStorefrontData;

    public function __construct(private readonly CartService $cart) {}

    public function show(Request $request): View
    {
        $user = $request->user()->loadCount(['favourites', 'orders']);

        return view('account.show', [
            ...$this->sharedData(),
            'user' => $user,
            'vendor' => $user->vendor,
            'application' => $user->vendorApplications()->latest()->first(),
            'cartQuantity' => $this->cart->count($user),
        ]);
    }

    public function updateProfile(UpdateProfileRequest $request): RedirectResponse
    {
        $user = $request->user();
        $data = $request->validated();

        if ($user->email !== $data['email']) {
            $user->email_verified_at = null;
        }

        $user->fill($data)->save();

        return back()->with('status', 'Your account details have been updated.');
    }

    public function updatePassword(UpdatePasswordRequest $request): RedirectResponse
    {
        $request->user()->update([
            'password' => $request->validated('password'),
        ]);

        return back()->with('status', 'Your password has been updated.');
    }
}
