@php
    $initialView = old('_form') === 'register' ? 'register' : 'login';
    $autoOpen = $errors->any() && old('_form') ? $initialView : null;
@endphp

<div
    id="auth-modal"
    data-auth-modal
    data-initial-view="{{ $initialView }}"
    @if ($autoOpen) data-auto-open="{{ $autoOpen }}" @endif
    class="group fixed inset-0 z-[95] hidden"
>
    <div
        data-auth-close
        class="absolute inset-0 bg-black/55 opacity-0 backdrop-blur-[3px] transition-opacity duration-300 group-data-[open=true]:opacity-100"
    ></div>

    <div class="absolute inset-0 flex items-end justify-center p-0 sm:items-center sm:p-5">
        <div
            role="dialog"
            aria-modal="true"
            aria-label="Vefiri account"
            class="relative max-h-[94vh] w-full max-w-md translate-y-5 scale-[0.98] overflow-y-auto rounded-t-3xl bg-white p-5 opacity-0 shadow-2xl transition duration-300 ease-out group-data-[open=true]:translate-y-0 group-data-[open=true]:scale-100 group-data-[open=true]:opacity-100 sm:rounded-3xl sm:p-6"
        >
            <div class="flex items-center justify-between gap-3">
                <x-ui.logo />
                <button
                    type="button"
                    data-auth-close
                    aria-label="Close account dialog"
                    class="flex size-9 items-center justify-center rounded-full text-muted transition hover:bg-surface hover:text-ink"
                >
                    <x-ui.icon name="close" class="size-5" />
                </button>
            </div>

            <div class="mt-5 rounded-2xl bg-surface p-1">
                <div class="grid grid-cols-2 gap-1" role="tablist" aria-label="Account options">
                    <button
                        type="button"
                        role="tab"
                        data-auth-tab="login"
                        data-active="{{ $initialView === 'login' ? 'true' : 'false' }}"
                        aria-selected="{{ $initialView === 'login' ? 'true' : 'false' }}"
                        class="h-9 rounded-xl text-xs font-bold text-muted transition data-[active=true]:bg-white data-[active=true]:text-ink data-[active=true]:shadow-sm"
                    >
                        Sign in
                    </button>
                    <button
                        type="button"
                        role="tab"
                        data-auth-tab="register"
                        data-active="{{ $initialView === 'register' ? 'true' : 'false' }}"
                        aria-selected="{{ $initialView === 'register' ? 'true' : 'false' }}"
                        class="h-9 rounded-xl text-xs font-bold text-muted transition data-[active=true]:bg-white data-[active=true]:text-ink data-[active=true]:shadow-sm"
                    >
                        Register
                    </button>
                </div>
            </div>

            <div data-auth-panel="login" @class(['mt-5', 'hidden' => $initialView !== 'login'])>
                <h2 class="text-xl font-black tracking-tight text-ink">Welcome back</h2>
                <p class="mt-1 text-xs leading-5 text-muted">Sign in to track orders, save favourites and unlock member prices.</p>

                <div class="mt-5">
                    <x-auth.login-form prefix="modal-login" />
                </div>
            </div>

            <div data-auth-panel="register" @class(['mt-5', 'hidden' => $initialView !== 'register'])>
                <h2 class="text-xl font-black tracking-tight text-ink">Create your account</h2>
                <p class="mt-1 text-xs leading-5 text-muted">Join Vefiri for exclusive coupons, faster checkout and buyer protection.</p>

                <div class="mt-5">
                    <x-auth.register-form prefix="modal-register" />
                </div>
            </div>

            <div class="mt-5 flex flex-wrap items-center justify-center gap-x-4 gap-y-2 border-t border-line pt-4 text-[10px] text-muted">
                <span class="inline-flex items-center gap-1">
                    <x-ui.icon name="shield" class="size-3 text-choice-dark" />
                    Secure account
                </span>
                <span class="inline-flex items-center gap-1">
                    <x-ui.icon name="truck" class="size-3 text-choice-dark" />
                    Nationwide delivery
                </span>
                <span class="inline-flex items-center gap-1">
                    <x-ui.icon name="rotate" class="size-3 text-choice-dark" />
                    Easy returns
                </span>
            </div>
        </div>
    </div>
</div>
