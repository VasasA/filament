@php
    use Filament\Support\Facades\FilamentView;

    $id = $getId();
    $key = $getKey();
    $statePath = $getStatePath();
@endphp

<x-dynamic-component :component="$getFieldWrapperView()" :field="$field">
    <div
        @if (FilamentView::hasSpaMode())
            {{-- format-ignore-start --}}x-load="visible || event (x-modal-opened)"{{-- format-ignore-end --}}
        @else
            x-load
        @endif
        x-load-src="{{ \Filament\Support\Facades\FilamentAsset::getAlpineComponentSrc('rich-editor', 'filament/forms') }}"
        x-data="richEditorFormComponent({
                    key: @js($key),
                    livewireId: @js($this->getId()),
                    state: $wire.{{ $applyStateBindingModifiers("\$entangle('{$statePath}')", isOptimisticallyLive: false) }},
                    statePath: @js($statePath),
                    uploadingFileMessage: @js($getUploadingFileMessage()),
                })"
        x-cloak
        x-bind:class="{
            'pointer-events-none opacity-50 cursor-wait': isUploadingFile,
        }"
    >
        <x-filament::input.wrapper
            :valid="! $errors->has($statePath)"
            :attributes="
                \Filament\Support\prepare_inherited_attributes($getExtraAttributeBag())
                    ->class(['fi-fo-rich-editor'])
            "
        >
            <div
                @class([
                    'fi-fo-rich-editor-toolbar relative flex flex-col gap-x-3 border-b border-gray-100 px-2.5 py-2 dark:border-white/10',
                    'hidden' => ! count($getToolbarButtons()),
                ])
            >
                <div class="flex gap-x-3 overflow-x-auto">
                    @if ($hasToolbarButton(['bold', 'italic', 'underline', 'strike', 'link']))
                        <x-filament-forms::rich-editor.toolbar.group>
                            @if ($hasToolbarButton('bold'))
                                <x-filament-forms::rich-editor.toolbar.button
                                    type="bold"
                                    x-on:click="getEditor().chain().focus().toggleBold().run()"
                                    :title="__('filament-forms::components.rich_editor.toolbar_buttons.bold')"
                                    tabindex="-1"
                                >
                                    <x-filament::icon
                                        :icon="\Filament\Support\Icons\Heroicon::Bold"
                                        class="h-5 w-5"
                                    />
                                </x-filament-forms::rich-editor.toolbar.button>
                            @endif

                            @if ($hasToolbarButton('italic'))
                                <x-filament-forms::rich-editor.toolbar.button
                                    type="italic"
                                    x-on:click="getEditor().chain().focus().toggleItalic().run()"
                                    title="{{ __('filament-forms::components.rich_editor.toolbar_buttons.italic') }}"
                                    tabindex="-1"
                                >
                                    <x-filament::icon
                                        :icon="\Filament\Support\Icons\Heroicon::Italic"
                                        class="h-5 w-5"
                                    />
                                </x-filament-forms::rich-editor.toolbar.button>
                            @endif

                            @if ($hasToolbarButton('underline'))
                                <x-filament-forms::rich-editor.toolbar.button
                                    type="underline"
                                    x-on:click="getEditor().chain().focus().toggleUnderline().run()"
                                    title="{{ __('filament-forms::components.rich_editor.toolbar_buttons.underline') }}"
                                    tabindex="-1"
                                >
                                    <x-filament::icon
                                        :icon="\Filament\Support\Icons\Heroicon::Underline"
                                        class="h-5 w-5"
                                    />
                                </x-filament-forms::rich-editor.toolbar.button>
                            @endif

                            @if ($hasToolbarButton('strike'))
                                <x-filament-forms::rich-editor.toolbar.button
                                    type="strike"
                                    x-on:click="getEditor().chain().focus().toggleStrike().run()"
                                    title="{{ __('filament-forms::components.rich_editor.toolbar_buttons.strike') }}"
                                    tabindex="-1"
                                >
                                    <x-filament::icon
                                        :icon="\Filament\Support\Icons\Heroicon::Strikethrough"
                                        class="h-5 w-5"
                                    />
                                </x-filament-forms::rich-editor.toolbar.button>
                            @endif

                            @if ($hasToolbarButton('sub'))
                                <x-filament-forms::rich-editor.toolbar.button
                                    type="sub"
                                    x-on:click="getEditor().chain().focus().toggleSubscript().run()"
                                    title="{{ __('filament-forms::components.rich_editor.toolbar_buttons.sub') }}"
                                    tabindex="-1"
                                >
                                    <svg
                                        class="-mx-4 h-4 dark:fill-current"
                                        aria-hidden="true"
                                        focusable="false"
                                        data-prefix="fas"
                                        data-icon="sub"
                                        role="img"
                                        xmlns="http://www.w3.org/2000/svg"
                                        viewBox="0 0 512 512"
                                    >
                                        <path
                                            fill="currentColor"
                                            d="M496 448h-16V304a16 16 0 0 0-16-16h-48a16 16 0 0 0-14.29 8.83l-16 32A16 16 0 0 0 400 352h16v96h-16a16 16 0 0 0-16 16v32a16 16 0 0 0 16 16h96a16 16 0 0 0 16-16v-32a16 16 0 0 0-16-16zM336 64h-67a16 16 0 0 0-13.14 6.87l-79.9 115-79.9-115A16 16 0 0 0 83 64H16A16 16 0 0 0 0 80v48a16 16 0 0 0 16 16h33.48l77.81 112-77.81 112H16a16 16 0 0 0-16 16v48a16 16 0 0 0 16 16h67a16 16 0 0 0 13.14-6.87l79.9-115 79.9 115A16 16 0 0 0 269 448h67a16 16 0 0 0 16-16v-48a16 16 0 0 0-16-16h-33.48l-77.81-112 77.81-112H336a16 16 0 0 0 16-16V80a16 16 0 0 0-16-16z"
                                        />
                                    </svg>
                                </x-filament-forms::rich-editor.toolbar.button>
                            @endif

                            @if ($hasToolbarButton('sup'))
                                <x-filament-forms::rich-editor.toolbar.button
                                    type="sup"
                                    x-on:click="getEditor().chain().focus().toggleSuperscript().run()"
                                    title="{{ __('filament-forms::components.rich_editor.toolbar_buttons.sup') }}"
                                    tabindex="-1"
                                >
                                    <svg
                                        class="-mx-4 h-4 dark:fill-current"
                                        aria-hidden="true"
                                        focusable="false"
                                        data-prefix="fas"
                                        data-icon="sup"
                                        role="img"
                                        xmlns="http://www.w3.org/2000/svg"
                                        viewBox="0 0 512 512"
                                    >
                                        <path
                                            fill="currentColor"
                                            d="M496 160h-16V16a16 16 0 0 0-16-16h-48a16 16 0 0 0-14.29 8.83l-16 32A16 16 0 0 0 400 64h16v96h-16a16 16 0 0 0-16 16v32a16 16 0 0 0 16 16h96a16 16 0 0 0 16-16v-32a16 16 0 0 0-16-16zM336 64h-67a16 16 0 0 0-13.14 6.87l-79.9 115-79.9-115A16 16 0 0 0 83 64H16A16 16 0 0 0 0 80v48a16 16 0 0 0 16 16h33.48l77.81 112-77.81 112H16a16 16 0 0 0-16 16v48a16 16 0 0 0 16 16h67a16 16 0 0 0 13.14-6.87l79.9-115 79.9 115A16 16 0 0 0 269 448h67a16 16 0 0 0 16-16v-48a16 16 0 0 0-16-16h-33.48l-77.81-112 77.81-112H336a16 16 0 0 0 16-16V80a16 16 0 0 0-16-16z"
                                        />
                                    </svg>
                                </x-filament-forms::rich-editor.toolbar.button>
                            @endif

                            @if ($hasToolbarButton('link'))
                                <x-filament-forms::rich-editor.toolbar.button
                                    type="link"
                                    :x-on:click="$getAction('link')->getAlpineClickHandler()"
                                    title="{{ __('filament-forms::components.rich_editor.toolbar_buttons.link') }}"
                                    tabindex="-1"
                                >
                                    <x-filament::icon
                                        :icon="\Filament\Support\Icons\Heroicon::Link"
                                        class="h-5 w-5"
                                    />
                                </x-filament-forms::rich-editor.toolbar.button>
                            @endif
                        </x-filament-forms::rich-editor.toolbar.group>
                    @endif

                    @if ($hasToolbarButton(['h1', 'h2', 'h3']))
                        <x-filament-forms::rich-editor.toolbar.group>
                            @if ($hasToolbarButton('h1'))
                                <x-filament-forms::rich-editor.toolbar.button
                                    type="heading"
                                    :active-options="['level' => 1]"
                                    x-on:click="getEditor().chain().focus().toggleHeading({ level: 1 }).run()"
                                    title="{{ __('filament-forms::components.rich_editor.toolbar_buttons.h1') }}"
                                    tabindex="-1"
                                >
                                    <x-filament::icon
                                        :icon="\Filament\Support\Icons\Heroicon::H1"
                                        class="h-5 w-5"
                                    />
                                </x-filament-forms::rich-editor.toolbar.button>
                            @endif

                            @if ($hasToolbarButton('h2'))
                                <x-filament-forms::rich-editor.toolbar.button
                                    type="heading"
                                    :active-options="['level' => 2]"
                                    x-on:click="getEditor().chain().focus().toggleHeading({ level: 2 }).run()"
                                    title="{{ __('filament-forms::components.rich_editor.toolbar_buttons.h2') }}"
                                    tabindex="-1"
                                >
                                    <x-filament::icon
                                        :icon="\Filament\Support\Icons\Heroicon::H2"
                                        class="h-5 w-5"
                                    />
                                </x-filament-forms::rich-editor.toolbar.button>
                            @endif

                            @if ($hasToolbarButton('h3'))
                                <x-filament-forms::rich-editor.toolbar.button
                                    type="heading"
                                    :active-options="['level' => 3]"
                                    x-on:click="getEditor().chain().focus().toggleHeading({ level: 3 }).run()"
                                    title="{{ __('filament-forms::components.rich_editor.toolbar_buttons.h3') }}"
                                    tabindex="-1"
                                >
                                    <x-filament::icon
                                        :icon="\Filament\Support\Icons\Heroicon::H3"
                                        class="h-5 w-5"
                                    />
                                </x-filament-forms::rich-editor.toolbar.button>
                            @endif
                        </x-filament-forms::rich-editor.toolbar.group>
                    @endif

                    @if ($hasToolbarButton(['blockquote', 'codeBlock', 'bulletList', 'orderedList']))
                        <x-filament-forms::rich-editor.toolbar.group>
                            @if ($hasToolbarButton('blockquote'))
                                <x-filament-forms::rich-editor.toolbar.button
                                    type="blockquote"
                                    x-on:click="getEditor().chain().focus().toggleBlockquote().run()"
                                    title="{{ __('filament-forms::components.rich_editor.toolbar_buttons.blockquote') }}"
                                    tabindex="-1"
                                >
                                    <x-filament::icon
                                        :icon="\Filament\Support\Icons\Heroicon::ChatBubbleBottomCenterText"
                                        class="h-5 w-5"
                                    />
                                </x-filament-forms::rich-editor.toolbar.button>
                            @endif

                            @if ($hasToolbarButton('codeBlock'))
                                <x-filament-forms::rich-editor.toolbar.button
                                    type="codeBlock"
                                    x-on:click="getEditor().chain().focus().toggleCodeBlock().run()"
                                    title="{{ __('filament-forms::components.rich_editor.toolbar_buttons.code_block') }}"
                                    tabindex="-1"
                                >
                                    <x-filament::icon
                                        :icon="\Filament\Support\Icons\Heroicon::CodeBracket"
                                        class="h-5 w-5"
                                    />
                                </x-filament-forms::rich-editor.toolbar.button>
                            @endif

                            @if ($hasToolbarButton('bulletList'))
                                <x-filament-forms::rich-editor.toolbar.button
                                    type="bulletList"
                                    x-on:click="getEditor().chain().focus().toggleBulletList().run()"
                                    title="{{ __('filament-forms::components.rich_editor.toolbar_buttons.bullet_list') }}"
                                    tabindex="-1"
                                >
                                    <x-filament::icon
                                        :icon="\Filament\Support\Icons\Heroicon::ListBullet"
                                        class="h-5 w-5"
                                    />
                                </x-filament-forms::rich-editor.toolbar.button>
                            @endif

                            @if ($hasToolbarButton('orderedList'))
                                <x-filament-forms::rich-editor.toolbar.button
                                    type="orderedList"
                                    x-on:click="getEditor().chain().focus().toggleOrderedList().run()"
                                    title="{{ __('filament-forms::components.rich_editor.toolbar_buttons.ordered_list') }}"
                                    tabindex="-1"
                                >
                                    <x-filament::icon
                                        :icon="\Filament\Support\Icons\Heroicon::NumberedList"
                                        class="h-5 w-5"
                                    />
                                </x-filament-forms::rich-editor.toolbar.button>
                            @endif
                        </x-filament-forms::rich-editor.toolbar.group>
                    @endif

                    @if ($hasToolbarButton('attachFiles'))
                        <x-filament-forms::rich-editor.toolbar.group>
                            <x-filament-forms::rich-editor.toolbar.button
                                type="image"
                                :x-on:click="$getAction('attachFiles')->getAlpineClickHandler()"
                                title="{{ __('filament-forms::components.rich_editor.toolbar_buttons.attach_files') }}"
                                tabindex="-1"
                            >
                                <x-filament::icon
                                    :icon="\Filament\Support\Icons\Heroicon::Photo"
                                    class="h-5 w-5"
                                />
                            </x-filament-forms::rich-editor.toolbar.button>
                        </x-filament-forms::rich-editor.toolbar.group>
                    @endif

                    @if ($hasToolbarButton(['undo', 'redo']))
                        <x-filament-forms::rich-editor.toolbar.group>
                            @if ($hasToolbarButton('undo'))
                                <x-filament-forms::rich-editor.toolbar.button
                                    x-on:click="getEditor().chain().focus().undo().run()"
                                    title="{{ __('filament-forms::components.rich_editor.toolbar_buttons.undo') }}"
                                    tabindex="-1"
                                >
                                    <x-filament::icon
                                        :icon="\Filament\Support\Icons\Heroicon::ArrowUturnLeft"
                                        class="h-5 w-5"
                                    />
                                </x-filament-forms::rich-editor.toolbar.button>
                            @endif

                            @if ($hasToolbarButton('redo'))
                                <x-filament-forms::rich-editor.toolbar.button
                                    x-on:click="getEditor().chain().focus().redo().run()"
                                    title="{{ __('filament-forms::components.rich_editor.toolbar_buttons.redo') }}"
                                    tabindex="-1"
                                >
                                    <x-filament::icon
                                        :icon="\Filament\Support\Icons\Heroicon::ArrowUturnRight"
                                        class="h-5 w-5"
                                    />
                                </x-filament-forms::rich-editor.toolbar.button>
                            @endif
                        </x-filament-forms::rich-editor.toolbar.group>
                    @endif
                </div>
            </div>

            <div
                class="prose min-h-full w-full max-w-none px-5"
                x-ref="editor"
                wire:ignore
            ></div>
        </x-filament::input.wrapper>
    </div>
</x-dynamic-component>
