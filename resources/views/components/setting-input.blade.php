@props(['label', 'name', 'type' => 'text', 'value' => ''])
<div class="mb-3">
    <label class="form-label">{{ $label }}</label>
    <input type="{{ $type }}" class="form-control" name="{{ $name }}" value="{{ old($name, $value) }}">
</div>
