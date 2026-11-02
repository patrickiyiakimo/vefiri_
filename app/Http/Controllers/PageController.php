<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Concerns\ProvidesStorefrontData;
use Illuminate\Contracts\View\View;

class PageController extends Controller
{
    use ProvidesStorefrontData;

    public function privacy(): View
    {
        return view('pages.privacy', $this->sharedData());
    }

    public function terms(): View
    {
        return view('pages.terms', $this->sharedData());
    }

    public function returns(): View
    {
        return view('pages.returns', $this->sharedData());
    }
}
