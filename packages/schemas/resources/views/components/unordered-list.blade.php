<ul
    @class([
        'fi-sc-unordered-list ms-3 list-disc sm:columns-2',
        match ($size = $getSize()) {
            'xs' => 'text-xs',
            null => 'text-sm',
            default => ($size instanceof \Filament\Support\Enums\TextSize) ? $size->value : $size,
        },
    ])
>
    @foreach ($getChildComponentContainer()->getComponents() as $component)
        <li>
            {{ $component }}
        </li>
    @endforeach
</ul>
