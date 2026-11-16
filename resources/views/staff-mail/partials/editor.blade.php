@push('styles')
    <style>
        .ck-editor__editable {
            min-height: 230px;
        }

        .mail-reply-editor .ck-editor__editable {
            min-height: 160px;
        }
    </style>
@endpush

@once
    @push('styles')
        @vite('resources/js/staff-mail-editor.js')
    @endpush
@endonce
