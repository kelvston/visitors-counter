@props(['label', 'name', 'value' => '', 'options' => []])
<div class="mb-3">
    <label class="form-label">{{ $label }}</label>
    <select class="form-select" name="{{ $name }}">
        @foreach($options as $opt)
            <option value="{{ $opt }}" @selected($value == $opt)>{{ ucfirst($opt) }}</option>
        @endforeach
    </select>
</div>
