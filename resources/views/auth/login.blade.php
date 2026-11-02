@extends('layouts.auth')

@section('title', 'Sign in | Vefiri')
@section('description', 'Sign in to your Vefiri account to track orders, save favourites and shop with buyer protection.')

@section('content')
    <div class="rounded-3xl border border-black/[0.04] bg-white p-6 shadow-card sm:p-8">
        <span class="flex size-11 items-center justify-center rounded-2xl bg-brand-50 text-brand-600">
            <x-ui.icon name="user" class="size-5" />
        </span>

        <h1 class="mt-5 text-2xl font-black tracking-tight text-ink">Welcome back</h1>
        <p class="mt-1.5 text-xs leading-5 text-muted">Sign in to continue shopping with Vefiri.</p>

        <div class="mt-6">
            <x-auth.login-form prefix="page-login" context="page" />
        </div>
    </div>
@endsection
