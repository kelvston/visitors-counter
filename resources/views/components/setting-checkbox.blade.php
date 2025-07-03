@props(['label', 'name', 'checked' => false])
<div class="form-check mb-3">
    <input type="checkbox" class="form-check-input" id="{{ $name }}" name="{{ $name }}" value="on" {{ old($name, $checked) ? 'checked' : '' }}>
    <label class="form-check-label" for="{{ $name }}">{{ $label }}</label>
</div>
