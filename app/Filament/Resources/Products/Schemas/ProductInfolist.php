<?php

namespace App\Filament\Resources\Products\Schemas;

use Filament\Infolists\Components\KeyValueEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class ProductInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('اطلاعات محصول')
                ->components([
                    TextEntry::make('name')
                        ->label(__('Product Name')),

                    TextEntry::make('price')
                        ->label(__('Price')),

                    TextEntry::make('is_active')
                        ->label(__('Status'))
                        ->badge()
                        ->formatStateUsing(fn ($state) => $state ? __('Active') : 'غیرفعال'),
                ])
                ->columns(2)
            ,

            Section::make('ویژگی‌های اضافی')
                ->components([
                    KeyValueEntry::make('extras')
                        ->label(__('Features')),
                ])
                ->collapsible()
                ->collapsible(),
        ]);
    }
}
