@props([
    'name' => 'password',
    'id' => null,
    'label',
    'autocomplete' => 'current-password',
])

@php
    $fieldId = $id ?? $name;
    $inputClass = 'form-control'.($errors->has($name) ? ' is-invalid' : '');
@endphp

<div class="mb-3">
    <label for="{{ $fieldId }}" class="form-label">{{ $label }}</label>
    <div class="input-group has-validation">
        <input
            type="password"
            name="{{ $name }}"
            id="{{ $fieldId }}"
            autocomplete="{{ $autocomplete }}"
            {{ $attributes->merge(['class' => $inputClass]) }}
        >
        <button
            type="button"
            class="btn btn-outline-secondary"
            data-password-toggle="{{ $fieldId }}"
            aria-label="Покажи паролата"
            tabindex="-1"
        >
            <i class="bi bi-eye" aria-hidden="true"></i>
        </button>
        @error($name)
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>
</div>
