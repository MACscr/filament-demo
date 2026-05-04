<?php

namespace App\Filament\Resources\Blog\Posts\Pages;

use App\Filament\Resources\Blog\Posts\PostResource;
use App\Models\Blog\Post;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Filament\Schemas\Components\Tabs\Tab;
use Illuminate\Database\Eloquent\Builder;

class ListPosts extends ListRecords
{
    protected static string $resource = PostResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }

    public function getTabs(): array
    {
        return [
            'all' => Tab::make('All')
                ->badge(fn () => Post::query()->count())
                ->deferBadge(),
            'published' => Tab::make('Published')
                ->badge(fn () => Post::query()->whereNotNull('published_at')->count())
                ->deferBadge()
                ->modifyQueryUsing(fn (Builder $query) => $query->whereNotNull('published_at')),
            'draft' => Tab::make('Draft')
                ->badge(fn () => Post::query()->whereNull('published_at')->count())
                ->deferBadge()
                ->modifyQueryUsing(fn (Builder $query) => $query->whereNull('published_at')),
        ];
    }
}
