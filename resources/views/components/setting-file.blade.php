@props(['label', 'name'])
<div class="mb-3">
    <label class="form-label">{{ $label }}</label>
    <input type="file" class="form-control" name="{{ $name }}">
</div>
