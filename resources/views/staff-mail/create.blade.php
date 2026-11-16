@extends('layouts.header')

@section('content')
    <div class="container mt-4 mail-page">
        <div class="mail-workspace">
            @include('staff-mail.partials.sidebar', ['folder' => null])
            <main class="mail-main mail-compose-main">
                <div class="mail-toolbar d-flex flex-wrap justify-content-between align-items-center gap-2 pb-3 mb-3">
                    <div>
                        <div class="mail-compose-title-icon" aria-hidden="true">
                            <svg viewBox="0 0 24 24" fill="none">
                                <path d="M4 5.5h16v13H4z" stroke="currentColor" stroke-width="1.7" stroke-linejoin="round"/>
                                <path d="m4.5 6 7.5 6 7.5-6" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                        </div>
                        <h1 class="h3 mail-page-heading mb-1">New message</h1>
                        <p class="text-muted mb-0">Write to any active staff member</p>
                    </div>
                </div>

                @if ($recipients->isEmpty())
                    <div class="alert alert-info">
                        There are no other active staff members available to message.
                    </div>
                @endif

                <form action="{{ route('staff-mail.store') }}" method="POST" enctype="multipart/form-data"
                      class="card card-body mail-compose-card">
                    @csrf
                    <div class="mail-compose-field">
                        @include('staff-mail.partials.recipient-picker', [
                            'field' => 'to',
                            'label' => 'To',
                            'required' => true,
                            'recipients' => $recipients,
                        ])
                    </div>

                    <details class="mail-copy-fields" @if (old('cc') || old('bcc')) open @endif>
                        <summary><span>Add CC or BCC</span><small>Optional recipients</small></summary>
                        <div class="row g-3 mt-1">
                            <div class="col-md-6">
                                @include('staff-mail.partials.recipient-picker', [
                                    'field' => 'cc',
                                    'label' => 'CC',
                                    'required' => false,
                                    'recipients' => $recipients,
                                ])
                            </div>
                            <div class="col-md-6">
                                @include('staff-mail.partials.recipient-picker', [
                                    'field' => 'bcc',
                                    'label' => 'BCC',
                                    'required' => false,
                                    'recipients' => $recipients,
                                ])
                                <small class="form-text text-muted mt-2 d-block">BCC recipients are hidden from other recipients.</small>
                            </div>
                        </div>
                    </details>

                    <div class="mail-compose-field">
                        <label for="subject" class="form-label">Subject</label>
                        <input id="subject" name="subject" type="text" class="form-control"
                               value="{{ old('subject') }}" maxlength="255" required>
                        @error('subject') <small class="text-danger">{{ $message }}</small> @enderror
                    </div>

                    <div class="mail-compose-field mail-compose-body">
                        <label for="body" class="form-label">Message</label>
                        <textarea id="body" name="body" class="form-control" rows="8" maxlength="50000"
                                  data-mail-editor>{{ old('body') }}</textarea>
                        @error('body') <small class="text-danger">{{ $message }}</small> @enderror
                    </div>
                    @include('staff-mail.partials.editor', ['editorId' => 'body'])

                    <div class="mail-compose-field">
                        <label for="attachment" class="form-label">Attachment <span class="text-muted fw-normal">(optional, max 10 MB)</span></label>
                        <label class="mail-attachment-dropzone" for="attachment">
                            <span class="mail-attachment-icon" aria-hidden="true">
                                <svg viewBox="0 0 24 24" fill="none">
                                    <path d="M12 16V4m0 0L7.5 8.5M12 4l4.5 4.5M5 14.5v4h14v-4"
                                          stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"/>
                                </svg>
                            </span>
                            <span><strong>Choose a file</strong> or browse from your device
                                <small>PDF, Office documents, text files, or images</small>
                            </span>
                            <input id="attachment" name="attachment" type="file"
                                   accept=".pdf,.doc,.docx,.xls,.xlsx,.ppt,.pptx,.txt,.jpg,.jpeg,.png">
                        </label>
                        <span class="mail-attachment-filename" data-attachment-filename hidden></span>
                        @error('attachment') <small class="text-danger">{{ $message }}</small> @enderror
                    </div>

                    <div class="mail-compose-actions">
                        <button type="submit" class="btn btn-primary mail-send-button" @disabled($recipients->isEmpty())>
                            <svg viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                <path d="M21 3 10.5 13.5M21 3l-6.5 18-4-7.5L3 9.5 21 3Z"
                                      stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                            Send message
                        </button>
                        <a href="{{ route('staff-mail.inbox') }}" class="btn btn-outline-secondary">Discard</a>
                    </div>
                </form>
            </main>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        document.querySelectorAll('[data-recipient-picker]').forEach(function (picker) {
            var search = picker.querySelector('[data-recipient-search]');
            var rows = picker.querySelectorAll('[data-recipient-option]');
            var count = picker.querySelector('[data-recipient-count]');
            var selectedContainer = picker.querySelector('[data-recipient-selected]');
            var options = picker.querySelector('[data-recipient-options]');
            var empty = picker.querySelector('[data-recipient-empty]');
            var type = picker.dataset.recipientType;
            var requiredMessage = picker.querySelector('[data-recipient-required]');

            function updatePicker() {
                var checked = Array.from(picker.querySelectorAll('input[type="checkbox"]:checked'));
                count.textContent = checked.length + ' selected';
                if (requiredMessage && checked.length > 0) requiredMessage.hidden = true;

                selectedContainer.replaceChildren();
                checked.forEach(function (checkbox) {
                    var row = checkbox.closest('[data-recipient-option]');
                    var chip = document.createElement('span');
                    var name = document.createElement('span');
                    var remove = document.createElement('button');
                    chip.className = 'mail-recipient-chip';
                    name.textContent = row.querySelector('strong').textContent;
                    remove.type = 'button';
                    remove.textContent = '×';
                    remove.setAttribute('aria-label', 'Remove ' + name.textContent);
                    remove.addEventListener('click', function () {
                        checkbox.checked = false;
                        updateRecipients();
                        search.focus();
                    });
                    chip.append(name, remove);
                    selectedContainer.append(chip);
                });

                var query = search.value.trim().toLowerCase();
                var visible = 0;
                rows.forEach(function (row) {
                    var checkbox = row.querySelector('input[type="checkbox"]');
                    var matches = query.length > 0 && row.dataset.search.includes(query) && !checkbox.checked;
                    row.hidden = !matches;
                    if (matches) visible++;
                });
                options.hidden = query.length === 0;
                empty.hidden = visible > 0;
            }

            search.addEventListener('input', updatePicker);
            picker.addEventListener('change', function (event) {
                if (event.target.matches('input[type="checkbox"]')) {
                    search.value = '';
                    updateRecipients();
                }
            });

            function updateRecipients() {
                var selectedIds = new Set(
                    Array.from(document.querySelectorAll('[data-recipient-picker] input[type="checkbox"]:checked'))
                        .map(function (checkbox) { return checkbox.value; })
                );
                document.querySelectorAll('[data-recipient-picker]').forEach(function (otherPicker) {
                    if (otherPicker.dataset.recipientType !== type) {
                        otherPicker.querySelectorAll('input[type="checkbox"]').forEach(function (checkbox) {
                            checkbox.disabled = !checkbox.checked && selectedIds.has(checkbox.value);
                        });
                    }
                    otherPicker.dispatchEvent(new Event('recipient-update'));
                });
            }

            picker.addEventListener('recipient-update', updatePicker);
            picker.addEventListener('focusout', function (event) {
                if (!picker.contains(event.relatedTarget)) {
                    options.hidden = true;
                    search.value = '';
                    updatePicker();
                }
            });
            picker.addEventListener('focusin', function () {
                if (search.value.trim()) updatePicker();
            });
            updatePicker();
        });

        var mailForm = document.querySelector('.mail-compose-card');
        mailForm.addEventListener('submit', function (event) {
            var toPicker = document.querySelector('[data-recipient-type="to"]');
            if (toPicker.querySelector('input[type="checkbox"]:checked')) return;

            event.preventDefault();
            toPicker.querySelector('[data-recipient-required]').hidden = false;
            toPicker.querySelector('[data-recipient-search]').focus();
        });

        var attachmentInput = document.getElementById('attachment');
        var attachmentName = document.querySelector('[data-attachment-filename]');
        attachmentInput.addEventListener('change', function () {
            var file = attachmentInput.files[0];
            attachmentName.hidden = !file;
            attachmentName.textContent = file
                ? file.name + ' · ' + Math.max(1, Math.ceil(file.size / 1024)) + ' KB selected'
                : '';
        });
    </script>
@endpush
