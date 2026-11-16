import {
    BlockQuote,
    Bold,
    ClassicEditor,
    Essentials,
    Heading,
    Italic,
    Link,
    List,
    Paragraph,
    Strikethrough,
    Underline,
} from 'ckeditor5';
import 'ckeditor5/ckeditor5.css';

document.querySelectorAll('[data-mail-editor]').forEach((textarea) => {
    const form = textarea.closest('form');

    ClassicEditor.create(textarea, {
        licenseKey: 'GPL',
        plugins: [
            Essentials,
            Paragraph,
            Bold,
            Italic,
            Underline,
            Strikethrough,
            Heading,
            Link,
            List,
            BlockQuote,
        ],
        toolbar: [
            'undo',
            'redo',
            '|',
            'heading',
            '|',
            'bold',
            'italic',
            'underline',
            'strikethrough',
            '|',
            'link',
            'bulletedList',
            'numberedList',
            'blockQuote',
        ],
        heading: {
            options: [
                { model: 'paragraph', title: 'Paragraph', class: 'ck-heading_paragraph' },
                { model: 'heading2', view: 'h2', title: 'Heading', class: 'ck-heading_heading2' },
                { model: 'heading3', view: 'h3', title: 'Subheading', class: 'ck-heading_heading3' },
            ],
        },
        link: {
            defaultProtocol: 'https://',
        },
    })
        .then((editor) => {
            const error = document.createElement('small');
            error.className = 'text-danger mt-2 mail-editor-validation-error';
            error.hidden = true;
            error.textContent = 'Please enter a message.';
            editor.ui.view.element.insertAdjacentElement('afterend', error);

            const getContent = () => editor.getData();
            const hasContent = () => getContent()
                .replace(/<[^>]*>/g, '')
                .replace(/&nbsp;|&#160;|&#xA0;|\u00a0/gi, '')
                .trim().length > 0;

            form.addEventListener('submit', (event) => {
                textarea.value = getContent();
                if (!hasContent()) {
                    event.preventDefault();
                    error.hidden = false;
                    editor.editing.view.focus();
                    return;
                }

                error.hidden = true;
            });

            editor.model.document.on('change:data', () => {
                if (hasContent()) error.hidden = true;
            });
        })
        .catch(() => {
            textarea.required = true;
            textarea.insertAdjacentHTML(
                'afterend',
                '<small class="text-warning">Rich-text editor could not load; plain text entry is still available.</small>',
            );
        });
});
