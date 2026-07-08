@props([
    'name',
    'label',
    'placeholder' => '',
    'value' => '',
    'required' => false
])

<div class="col-span-full flex flex-col">

    <label class="mb-2 text-sm font-medium text-gray-700">
        {{ $label }}

        @if($required)
            <span class="text-red-500">*</span>
        @endif
    </label>

    <textarea
        name="{{ $name }}"
        rows="3"
        placeholder="{{ $placeholder }}"
        @if($required) required @endif
        class="w-full rounded-lg border border-gray-300 px-4 py-2.5 text-sm text-gray-800 placeholder-gray-400 outline-none focus:ring-2 focus:ring-orange-500 focus:border-orange-500 transition">{{ old($name, $value) }}</textarea>

    @error($name)
        <small class="mt-1 text-red-500">
            {{ $message }}
        </small>
    @enderror

</div>
