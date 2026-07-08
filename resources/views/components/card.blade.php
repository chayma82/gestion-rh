@props(['accent' => false])

<div class="bg-white rounded-2xl border border-gray-200/60 shadow-[0_4px_25px_rgba(0,0,0,0.04)] p-6 md:p-8 mb-6 {{ $accent ? 'border-l-4 border-l-[#E2721B]' : '' }}">
    {{ $slot }}
</div>
