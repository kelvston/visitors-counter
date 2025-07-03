@props(['label', 'name', 'value' => ''])
<div class="mb-3">
    <label class="form-label">{{ $label }}</label>
    <textarea class="form-control" name="{{ $name }}" rows="3">{{ old($name, $value) }}</textarea>
</div>
