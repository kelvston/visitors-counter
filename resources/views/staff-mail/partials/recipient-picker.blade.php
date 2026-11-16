<div class="mail-recipient-picker" data-recipient-picker data-recipient-type="{{ $field }}">
    <div class="d-flex justify-content-between align-items-center gap-2 mb-1">
        <label class="form-label mb-0">{{ $label }} @if ($required)<span class="text-danger">*</span>@endif</label>
        <span class="mail-recipient-count" data-recipient-count>0 selected</span>
    </div>
    <div class="mail-recipient-selected" data-recipient-selected></div>
    <div class="mail-recipient-search-wrap">
        <input type="search" class="form-control mail-recipient-search"
               placeholder="Search staff by name or email" aria-label="Search {{ strtolower($label) }} recipients"
               autocomplete="off" data-recipient-search>
        <div class="mail-recipient-options" data-recipient-options hidden>
            @foreach ($recipients as $recipient)
                <label class="mail-recipient-option" data-recipient-option
                       data-search="{{ mb_strtolower($recipient->name.' '.$recipient->email) }}">
                    <input type="checkbox" class="form-check-input"
                           name="{{ $field }}[]" value="{{ $recipient->id }}"
                           @checked(in_array($recipient->id, (array) old($field, [])))>
                    <span class="mail-recipient-identity">
                        <strong>{{ $recipient->name }}</strong>
                        <small>{{ $recipient->email }}</small>
                    </span>
                </label>
            @endforeach
            <p class="mail-recipient-no-results" data-recipient-empty hidden>No matching staff found.</p>
        </div>
    </div>
    @if ($required)
        <p class="text-danger small mb-0 mt-2" data-recipient-required hidden>Select at least one recipient in To.</p>
    @endif
    @error($field) <small class="text-danger d-block mt-1">{{ $message }}</small> @enderror
    @error($field.'.*') <small class="text-danger d-block mt-1">{{ $message }}</small> @enderror
</div>
