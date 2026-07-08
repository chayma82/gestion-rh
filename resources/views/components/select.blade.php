@props([
    'name',
    'label',
    'options' => [],
    'value' => '',
    'required' => false
])

<div class="flex flex-col">

    <label class="mb-2 text-sm font-medium text-gray-700">
        {{ $label }}

        @if($required)
            <span class="text-red-500">*</span>
        @endif
    </label>

    <div class="relative">

        <select
            name="{{ $name }}"
            @if($required) required @endif
            class="w-full appearance-none rounded-lg border border-gray-300 px-4 py-2.5 pr-10 text-sm text-gray-800 bg-white outline-none focus:ring-2 focus:ring-orange-500 focus:border-orange-500 transition">

            <option value="" class="text-gray-400">Sélectionner...</option>

            @foreach($options as $option)
                <option
                    value="{{ $option }}"
                    {{ old($name, $value) == $option ? 'selected' : '' }}>
                    {{ $option }}
                </option>
            @endforeach

        </select>

        <i class="fa-solid fa-chevron-down absolute right-4 top-1/2 -translate-y-1/2 text-gray-400 text-xs pointer-events-none"></i>

    </div>

    @error($name)
        <small class="mt-1 text-red-500">
            {{ $message }}
        </small>
    @enderror

</div>
