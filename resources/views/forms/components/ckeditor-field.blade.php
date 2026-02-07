<x-dynamic-component :component="$getFieldWrapperView()" :field="$field">
    <div
        x-data="{
            state: $wire.$entangle('{{ $getStatePath() }}'),
            editor: null,
            init() {
                const textarea = this.$refs.editor;
                const initialValue = textarea.value || '';
                if (typeof CKEDITOR !== 'undefined') {
                    this.editor = CKEDITOR.replace(textarea, {
                        height: 300,
                        filebrowserUploadUrl: '{{ route('ckeditor.upload') }}',
                        filebrowserUploadMethod: 'form'
                    });
                    this.editor.on('instanceReady', () => {
                        this.editor.setData(initialValue);
                    });
                    this.editor.on('change', () => {
                        this.state = this.editor.getData();
                    });
                    this.$watch('state', (value) => {
                        if (this.editor && value !== this.editor.getData()) {
                            this.editor.setData(value || '');
                        }
                    });
                } else {
                    console.error('CKEditor is not loaded');
                }
            }
        }"
        wire:ignore
        {{ $attributes->merge($getExtraAttributes())->class(['filament-forms-field-wrapper']) }}
    >
        <textarea
            x-ref="editor"
            id="{{ $getId() }}"
            name="{{ $getName() }}"
        >{{ $getState() }}</textarea>
    </div>
{{--    @dd($getId(),$getState(),$getName())--}}
</x-dynamic-component>
