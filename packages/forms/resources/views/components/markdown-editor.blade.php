@php
    use Filament\Support\Facades\FilamentView;

    $key = $getKey();
    $statePath = $getStatePath();
@endphp

<x-dynamic-component :component="$getFieldWrapperView()" :field="$field">
    @if ($isDisabled())
        <div
            class="fi-fo-markdown-editor fi-disabled prose dark:prose-invert block w-full max-w-none rounded-lg bg-gray-50 px-3 py-3 text-gray-500 ring-1 shadow-xs ring-gray-950/10 sm:text-sm dark:bg-transparent dark:text-gray-400 dark:ring-white/10"
        >
            {!! str($getState())->markdown()->sanitizeHtml() !!}
        </div>
    @else
        <x-filament::input.wrapper
            :valid="! $errors->has($statePath)"
            :attributes="
                \Filament\Support\prepare_inherited_attributes($getExtraAttributeBag())
                    ->class(['fi-fo-markdown-editor max-w-full overflow-hidden font-mono text-base text-gray-950 dark:text-white sm:text-sm'])
            "
        >
            <div
                {{-- prettier-ignore-start --}}x-load="visible || event (x-modal-opened)"
                {{-- prettier-ignore-end --}}
                x-load-src="{{ \Filament\Support\Facades\FilamentAsset::getAlpineComponentSrc('markdown-editor', 'filament/forms') }}"
                x-data="markdownEditorFormComponent({
                            canAttachFiles: @js($hasToolbarButton('attachFiles')),
                            isLiveDebounced: @js($isLiveDebounced()),
                            isLiveOnBlur: @js($isLiveOnBlur()),
                            liveDebounce: @js($getNormalizedLiveDebounce()),
                            maxHeight: @js($getMaxHeight()),
                            minHeight: @js($getMinHeight()),
                            placeholder: @js($getPlaceholder()),
                            state: $wire.{{ $applyStateBindingModifiers("\$entangle('{$statePath}')", isOptimisticallyLive: false) }},
                            toolbarButtons: @js($getToolbarButtons()),
                            translations: @js(__('filament-forms::components.markdown_editor')),
                            uploadFileAttachmentUsing: async (file, onSuccess, onError) => {
                                $wire.upload(`componentFileAttachments.{{ $key }}`, file, () => {
                                    $wire
                                        .callSchemaComponentMethod(
                                            '{{ $key }}',
                                            'saveUploadedFileAttachment',
                                        )
                                        .then((url) => {
                                            if (! url) {
                                                return onError()
                                            }

                                            onSuccess(url)
                                        })
                                })
                            },
                        })"
                wire:ignore
                {{ $getExtraAlpineAttributeBag() }}
            >
                <textarea x-ref="editor" class="hidden"></textarea>
            </div>
        </x-filament::input.wrapper>
    @endif
</x-dynamic-component>
