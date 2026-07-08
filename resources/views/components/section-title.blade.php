@props([
    'number',
    'title',
    'icon' => 'fa-circle'
])

<div class="flex items-center gap-2.5 mb-6">

    <i class="fa-solid {{ $icon }} text-[#E2721B] text-sm"></i>

    <h3 class="text-base font-semibold text-gray-800">
                @if($number)
            {{ $number }}.
        @endif
        {{ $title }}

    </h3>

</div>
