<?php

namespace Filament\Tests\Fixtures\Resources\PostCategories;

use BackedEnum;
use Filament\Resources\Resource;
use Filament\Support\Icons\Heroicon;
use Filament\Tests\Fixtures\Models\PostCategory;
use Filament\Tests\Fixtures\Resources\PostCategories\Pages\CreatePostCategory;
use Filament\Tests\Fixtures\Resources\PostCategories\Pages\EditPostCategory;
use Filament\Tests\Fixtures\Resources\PostCategories\Pages\ListPostCategories;
use Filament\Tests\Fixtures\Resources\PostCategories\Pages\ViewPostCategory;

class PostCategoryResource extends Resource
{
    protected static ?string $model = PostCategory::class;

    protected static ?string $navigationGroup = 'Blog';

    protected static string | BackedEnum | null $navigationIcon = Heroicon::OutlinedRectangleStack;

    public static function getPages(): array
    {
        return [
            'index' => ListPostCategories::route('/'),
            'create' => CreatePostCategory::route('/create'),
            'view' => ViewPostCategory::route('/{record}'),
            'edit' => EditPostCategory::route('/{record}/edit'),
        ];
    }
}
