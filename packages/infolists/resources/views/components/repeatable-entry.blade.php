@php
    $isContained = $isContained();
@endphp

<x-dynamic-component :component="$getEntryWrapperView()" :entry="$entry">
    <div
        {{
            $attributes
                ->merge([
                    'id' => $getId(),
                ], escape: false)
                ->merge($getExtraAttributes(), escape: false)
                ->class([
                    'fi-in-repeatable',
                    'fi-contained' => $isContained,
                ])
        }}
    >
        @if (count($items = $getItems()))
            <ul>
                <div
                    {{ (new \Illuminate\View\ComponentAttributeBag)->grid($getGridColumns())->class(['gap-4']) }}
                >
                    @foreach ($items as $item)
                        <li
                            @class([
                                'fi-in-repeatable-item block',
                                'rounded-xl bg-white p-4 ring-1 shadow-xs ring-gray-950/5 dark:bg-white/5 dark:ring-white/10' => $isContained,
                            ])
                        >
                            {{ $item }}
                        </li>
                    @endforeach
                </div>
            </ul>
        @elseif (($placeholder = $getPlaceholder()) !== null)
            <x-filament-infolists::entries.placeholder>
                {{ $placeholder }}
            </x-filament-infolists::entries.placeholder>
        @endif
    </div>
</x-dynamic-component>
