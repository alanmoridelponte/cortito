{{-- Simplified Sol de Mayo: 16 triangular rays + solid disc. Purely decorative
     (no heraldic face). Size/color/animation come from the caller via class. --}}
<svg {{ $attributes->merge(['class' => 'text-sol']) }} viewBox="0 0 200 200" fill="currentColor" aria-hidden="true">
    @for ($i = 0; $i < 16; $i++)
        <polygon points="100,6 108,58 92,58" transform="rotate({{ $i * 22.5 }} 100 100)" />
    @endfor
    <circle cx="100" cy="100" r="36" />
</svg>
