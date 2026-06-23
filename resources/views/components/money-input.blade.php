@props(['name', 'value' => '', 'id' => null, 'required' => false, 'placeholder' => '0'])
@php
    // Nilai mentah (digit saja) untuk dikirim ke server, dan versi terformat untuk ditampilkan.
    $rawVal = ($value !== '' && $value !== null) ? (string) (int) round((float) $value) : '';
    $formatted = $rawVal === '' ? '' : number_format((int) $rawVal, 0, ',', '.');
    $inputId = $id ?? $name;
@endphp
<div x-data="{
        raw: @js($rawVal),
        format(e) {
            const digits = e.target.value.replace(/\D/g, '');
            this.raw = digits;
            e.target.value = digits === '' ? '' : Number(digits).toLocaleString('id-ID');
        }
     }" class="relative">
    <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-gray-400 text-sm pointer-events-none">Rp</span>
    <input type="text" inputmode="numeric" id="{{ $inputId }}"
           value="{{ $formatted }}" @input="format($event)" placeholder="{{ $placeholder }}"
           {{ $required ? 'required' : '' }}
           {{ $attributes->merge(['class' => 'mt-1 w-full rounded-lg border-gray-300 pl-9 focus:border-indigo-500 focus:ring-indigo-500']) }}>
    <input type="hidden" name="{{ $name }}" :value="raw">
</div>
