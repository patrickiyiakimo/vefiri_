@props([
    'name',
    'class' => 'size-5',
])

<svg
    xmlns="http://www.w3.org/2000/svg"
    viewBox="0 0 24 24"
    fill="none"
    stroke="currentColor"
    stroke-width="1.8"
    stroke-linecap="round"
    stroke-linejoin="round"
    aria-hidden="true"
    {{ $attributes->merge(['class' => $class]) }}
>
    @switch($name)
        @case('search')
            <circle cx="11" cy="11" r="7" />
            <path d="m20 20-3.5-3.5" />
            @break

        @case('camera')
            <path d="M14.5 5 13 3h-2L9.5 5H5a2 2 0 0 0-2 2v11a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V7a2 2 0 0 0-2-2Z" />
            <circle cx="12" cy="12.5" r="3.5" />
            @break

        @case('user')
            <circle cx="12" cy="8" r="3.5" />
            <path d="M4.5 21a7.5 7.5 0 0 1 15 0" />
            @break

        @case('cart')
            <circle cx="9" cy="20" r="1.25" />
            <circle cx="18" cy="20" r="1.25" />
            <path d="M3 4h2l2.4 11.2a2 2 0 0 0 2 1.6h7.9a2 2 0 0 0 2-1.6L21 8H6" />
            @break

        @case('heart')
            <path d="M20.8 5.8a5.3 5.3 0 0 0-7.5 0L12 7.1l-1.3-1.3a5.3 5.3 0 0 0-7.5 7.5L12 22l8.8-8.7a5.3 5.3 0 0 0 0-7.5Z" />
            @break

        @case('globe')
            <circle cx="12" cy="12" r="9" />
            <path d="M3 12h18" />
            <path d="M12 3c2.3 2.4 3.5 5.5 3.5 9s-1.2 6.6-3.5 9c-2.3-2.4-3.5-5.5-3.5-9S9.7 5.4 12 3Z" />
            @break

        @case('help')
            <circle cx="12" cy="12" r="9" />
            <path d="M9.7 9a2.4 2.4 0 1 1 3.4 2.2c-.8.4-1.1 1-1.1 1.8v.5" />
            <path d="M12 17h.01" />
            @break

        @case('chevron-down')
            <path d="m6 9 6 6 6-6" />
            @break

        @case('chevron-up')
            <path d="m6 15 6-6 6 6" />
            @break

        @case('chevron-left')
            <path d="m15 18-6-6 6-6" />
            @break

        @case('chevron-right')
            <path d="m9 18 6-6-6-6" />
            @break

        @case('arrow-right')
            <path d="M5 12h14" />
            <path d="m13 6 6 6-6 6" />
            @break

        @case('arrow-left')
            <path d="M19 12H5" />
            <path d="m11 18-6-6 6-6" />
            @break

        @case('menu')
            <path d="M4 7h16" />
            <path d="M4 12h16" />
            <path d="M4 17h16" />
            @break

        @case('close')
            <path d="m6 6 12 12" />
            <path d="m18 6-12 12" />
            @break

        @case('grid')
            <rect x="3" y="3" width="7" height="7" rx="1.5" />
            <rect x="14" y="3" width="7" height="7" rx="1.5" />
            <rect x="3" y="14" width="7" height="7" rx="1.5" />
            <rect x="14" y="14" width="7" height="7" rx="1.5" />
            @break

        @case('more-horizontal')
            <circle cx="5" cy="12" r="1" fill="currentColor" stroke="none" />
            <circle cx="12" cy="12" r="1" fill="currentColor" stroke="none" />
            <circle cx="19" cy="12" r="1" fill="currentColor" stroke="none" />
            @break

        @case('hanger')
            <path d="M12 7a2 2 0 1 1 2-2" />
            <path d="m12 7-1 .8L3.8 13a2 2 0 0 0 1.2 3.6h14a2 2 0 0 0 1.2-3.6L13 7.8 12 7Z" />
            @break

        @case('shirt')
            <path d="m8 4-5 3 2 5 2-1v9h10v-9l2 1 2-5-5-3a4 4 0 0 1-8 0Z" />
            @break

        @case('smartphone')
            <rect x="6" y="2.5" width="12" height="19" rx="2.5" />
            <path d="M10 18.5h4" />
            @break

        @case('headphones')
            <path d="M4 14v-2a8 8 0 0 1 16 0v2" />
            <path d="M4 14h2.5a1 1 0 0 1 1 1v4a1 1 0 0 1-1 1H5a2 2 0 0 1-2-2v-2a2 2 0 0 1 1-2Z" />
            <path d="M20 14h-2.5a1 1 0 0 0-1 1v4a1 1 0 0 0 1 1h1.5a2 2 0 0 0 2-2v-2a2 2 0 0 0-1-2Z" />
            @break

        @case('home')
            <path d="m3 11 9-7 9 7" />
            <path d="M5.5 9.5V20h13V9.5" />
            <path d="M9.5 20v-5.5h5V20" />
            @break

        @case('sparkles')
            <path d="m12 3 1.2 3.8L17 8l-3.8 1.2L12 13l-1.2-3.8L7 8l3.8-1.2L12 3Z" />
            <path d="m5 14 .8 2.2L8 17l-2.2.8L5 20l-.8-2.2L2 17l2.2-.8L5 14Z" />
            <path d="m19 14 .8 2.2L22 17l-2.2.8L19 20l-.8-2.2L16 17l2.2-.8L19 14Z" />
            @break

        @case('trophy')
            <path d="M8 4h8v5a4 4 0 0 1-8 0V4Z" />
            <path d="M8 6H4v2a4 4 0 0 0 4 4" />
            <path d="M16 6h4v2a4 4 0 0 1-4 4" />
            <path d="M12 13v4" />
            <path d="M8 21h8" />
            <path d="M9 17h6l1 4H8l1-4Z" />
            @break

        @case('puzzle')
            <path d="M9 3h4a1.5 1.5 0 0 1 0 3h-1v3h3V8a1.5 1.5 0 0 1 3 0v1h3v4a1.5 1.5 0 0 1-3 0v-1h-3v3h1a1.5 1.5 0 0 1 0 3h-1v3H9v-3H8a1.5 1.5 0 0 1 0-3h1v-3H6v1a1.5 1.5 0 0 1-3 0V8h3V7a1.5 1.5 0 0 1 3 0v1h3V6H9V3Z" />
            @break

        @case('wrench')
            <path d="M14.5 6.5a4.5 4.5 0 0 0 6 6L12 21a2.8 2.8 0 0 1-4-4l8.5-8.5a4.5 4.5 0 0 0-2-2Z" />
            <path d="m14.5 6.5 2-2" />
            @break

        @case('car')
            <path d="m5 11 1.5-4h11L19 11" />
            <path d="M4 11h16a2 2 0 0 1 2 2v4h-2a2 2 0 0 1-4 0H8a2 2 0 0 1-4 0H2v-4a2 2 0 0 1 2-2Z" />
            <path d="M7 17h10" />
            @break

        @case('badge-check')
            <path d="m12 2 2.2 2.2 3.1-.5.5 3.1L20 9l-1.4 2.7.5 3.1-3.1.5L13.8 18 12 16.4 10.2 18l-2.2-2.7-3.1-.5.5-3.1L4 9l1.4-2.7-.5-3.1 3.1.5L12 2Z" />
            <path d="m9 11 2 2 4-4" />
            @break

        @case('flame')
            <path d="M12 22c4 0 7-2.8 7-6.8 0-2.3-1.2-4.5-3.1-6.3.1 1.4-.5 2.7-1.6 3.5.2-3.7-1.8-6.8-4.7-9.4.2 3.2-1.5 4.8-2.8 6.4A7.6 7.6 0 0 0 5 15.2C5 19.2 8 22 12 22Z" />
            @break

        @case('gift')
            <rect x="3" y="8" width="18" height="4" rx="1" />
            <path d="M5 12v9h14v-9" />
            <path d="M12 8v13" />
            <path d="M12 8c-2.5 0-5-.7-5-2.7C7 3.6 8.4 3 9.4 3c2.3 0 2.6 2.7 2.6 5Z" />
            <path d="M12 8c2.5 0 5-.7 5-2.7 0-1.7-1.4-2.3-2.4-2.3-2.3 0-2.6 2.7-2.6 5Z" />
            @break

        @case('plug')
            <path d="M9 3v5" />
            <path d="M15 3v5" />
            <path d="M6 8h12v2a6 6 0 0 1-12 0V8Z" />
            <path d="M12 16v5" />
            @break

        @case('paw')
            <ellipse cx="6.2" cy="9.2" rx="1.7" ry="2.2" />
            <ellipse cx="17.8" cy="9.2" rx="1.7" ry="2.2" />
            <ellipse cx="9.5" cy="5.8" rx="1.7" ry="2.2" />
            <ellipse cx="14.5" cy="5.8" rx="1.7" ry="2.2" />
            <path d="M12 11c-3.7 0-6.3 3.2-6.3 5.7 0 2.1 1.5 3.3 3.5 3.3 1.2 0 1.9-.5 2.8-.5s1.6.5 2.8.5c2 0 3.5-1.2 3.5-3.3C18.3 14.2 15.7 11 12 11Z" />
            @break

        @case('watch')
            <circle cx="12" cy="12" r="5" />
            <path d="M9 7 8.5 3h7L15 7" />
            <path d="m15 17 .5 4h-7L9 17" />
            <path d="M12 9v3l2 1" />
            @break

        @case('bag')
            <path d="M5 8h14l1 13H4L5 8Z" />
            <path d="M9 9V6a3 3 0 0 1 6 0v3" />
            @break

        @case('truck')
            <path d="M3 6h11v11H3z" />
            <path d="M14 10h4l3 3v4h-7" />
            <circle cx="7" cy="19" r="2" />
            <circle cx="18" cy="19" r="2" />
            @break

        @case('briefcase')
            <rect x="3" y="7" width="18" height="13" rx="2" />
            <path d="M9 7V5a2 2 0 0 1 2-2h2a2 2 0 0 1 2 2v2" />
            <path d="M3 12h18" />
            @break

        @case('location')
            <path d="M20 10c0 5-8 12-8 12S4 15 4 10a8 8 0 1 1 16 0Z" />
            <circle cx="12" cy="10" r="2.5" />
            @break

        @case('package')
            <path d="m12 2 9 5-9 5-9-5 9-5Z" />
            <path d="m3 7 9 5 9-5" />
            <path d="M3 7v10l9 5 9-5V7" />
            <path d="M12 12v10" />
            @break

        @case('shield')
            <path d="M12 22s8-3.5 8-10V5l-8-3-8 3v7c0 6.5 8 10 8 10Z" />
            <path d="m9 12 2 2 4-4" />
            @break

        @case('check')
            <path d="m5 12 4.5 4.5L19 7" />
            @break

        @case('lightning')
            <path d="M13 2 4.5 13H11l-1 9 8.5-11H12l1-9Z" />
            @break

        @case('plus')
            <path d="M12 5v14" />
            <path d="M5 12h14" />
            @break

        @case('minus')
            <path d="M5 12h14" />
            @break

        @case('trash')
            <path d="M4 7h16" />
            <path d="M9 7V4h6v3" />
            <path d="M6 7v14h12V7" />
            <path d="M10 11v6" />
            <path d="M14 11v6" />
            @break

        @case('clock')
            <circle cx="12" cy="12" r="9" />
            <path d="M12 7v5l3 2" />
            @break

        @case('star')
            <path d="m12 2.5 2.9 6 6.6.9-4.8 4.6 1.2 6.5-5.9-3.1L6.1 20.5l1.2-6.5-4.8-4.6 6.6-.9 2.9-6Z" fill="currentColor" stroke="none" />
            @break

        @case('play')
            <path d="M8 5.5v13l10-6.5-10-6.5Z" />
            @break

        @case('rotate')
            <path d="M3 12a9 9 0 1 0 3-6.7" />
            <path d="M3 4v5h5" />
            @break

        @case('lock')
            <rect x="4" y="10" width="16" height="11" rx="2" />
            <path d="M8 10V7a4 4 0 0 1 8 0v3" />
            @break

        @case('eye')
            <path d="M2.5 12s3.5-6.5 9.5-6.5 9.5 6.5 9.5 6.5-3.5 6.5-9.5 6.5S2.5 12 2.5 12Z" />
            <circle cx="12" cy="12" r="3" />
            @break

        @case('eye-off')
            <path d="m3 3 18 18" />
            <path d="M10.6 10.6a2 2 0 0 0 2.8 2.8" />
            <path d="M9.9 5.2A10.9 10.9 0 0 1 12 5c6 0 9.5 6.5 9.5 6.5a18.4 18.4 0 0 1-3.2 4.2" />
            <path d="M6.1 6.1C3.7 7.8 2.5 12 2.5 12s3.5 6.5 9.5 6.5a10.9 10.9 0 0 0 4.1-.8" />
            @break

        @case('mail')
            <rect x="3" y="5" width="18" height="14" rx="2" />
            <path d="m3 7 9 6 9-6" />
            @break

        @case('download')
            <path d="M12 3v12" />
            <path d="m7 10 5 5 5-5" />
            <path d="M5 21h14" />
            @break

        @case('store')
            <path d="M4 10v10h16V10" />
            <path d="m3 4 2-2h14l2 2v3a3 3 0 0 1-6 0 3 3 0 0 1-6 0 3 3 0 0 1-6 0V4Z" />
            <path d="M9 20v-5h6v5" />
            @break

        @case('orders')
            <path d="M6 3h12v18l-3-2-3 2-3-2-3 2V3Z" />
            <path d="M9 8h6" />
            <path d="M9 12h6" />
            @break

        @case('facebook')
            <path d="M14 8.5V6.8c0-.8.5-1.3 1.3-1.3H17V2.2c-.7-.1-1.7-.2-2.6-.2-2.6 0-4.2 1.6-4.2 4.4v2.1H7v3.7h3.2V22H14v-9.8h3.1l.5-3.7H14Z" fill="currentColor" stroke="none" />
            @break

        @case('instagram')
            <rect x="3" y="3" width="18" height="18" rx="5" />
            <circle cx="12" cy="12" r="4" />
            <circle cx="17.5" cy="6.5" r="1" fill="currentColor" stroke="none" />
            @break

        @case('x')
            <path d="m4 4 16 16M20 4 4 20" />
            @break

        @case('youtube')
            <path d="M22 12c0-2.4-.2-4.1-.5-5.2-.2-.8-1-1.5-1.8-1.7C17.9 4.8 12 4.8 12 4.8s-5.9 0-7.7.3c-.8.2-1.6.9-1.8 1.7C2.2 7.9 2 9.6 2 12s.2 4.1.5 5.2c.2.8 1 1.5 1.8 1.7 1.8.3 7.7.3 7.7.3s5.9 0 7.7-.3c.8-.2 1.6-.9 1.8-1.7.3-1.1.5-2.8.5-5.2Z" fill="currentColor" stroke="none" />
            <path d="m10 8.5 5.5 3.5-5.5 3.5v-7Z" fill="white" stroke="none" />
            @break

        @case('tiktok')
            <path d="M14 3v11.2a4.3 4.3 0 1 1-3.7-4.3v3.2a1.2 1.2 0 1 0 .7 1.1V3h3Zm0 0c.5 2.4 2.1 4.1 4.7 4.5v3.1A8 8 0 0 1 14 8.5V3Z" fill="currentColor" stroke="none" />
            @break

        @case('headset')
            <path d="M4 14v-2a8 8 0 0 1 16 0v2" />
            <path d="M4 14h3v6H5a2 2 0 0 1-2-2v-2a2 2 0 0 1 1-2Z" />
            <path d="M20 14h-3v6h2a2 2 0 0 0 2-2v-2a2 2 0 0 0-1-2Z" />
            <path d="M17 20c0 1.1-1.3 2-3 2" />
            @break

        @default
            <circle cx="12" cy="12" r="9" />
    @endswitch
</svg>
