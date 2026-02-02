<?php

namespace App\Filament\Resources\Products\Schemas;

use Filament\Forms\Components\KeyValue;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Fieldset;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Schema;

class ProductForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Fieldset::make('اطلاعات اصلی')
                ->schema([
                    Grid::make(2)
                        ->schema([
                            TextInput::make('name')
                                ->label(__('Product Name'))
                                ->required()
                                ->maxLength(255)
                                ->columnSpan(2),

                            TextInput::make('price')
                                ->label(__('Price'))
                                ->numeric()
                                ->required()
                                ->prefix('تومان')
                                ->columnSpan(2)
                                ->step(100),
                        ]),

                    Toggle::make('is_active')
                        ->label(__('Active Status'))
                        ->default(true)
                        ->inline(false),
                ]),

            Fieldset::make('ویژگی‌های اضافی')
                ->schema([
                    Repeater::make('extras')
                        ->label(__('Features'))
                        ->schema([
                            Grid::make(2)
                                ->schema([
                                    TextInput::make('key')
                                        ->label(__('Key'))
                                        ->required()
                                        ->placeholder(__('Example: Color')),

                                    TextInput::make('value')
                                        ->label(__('Value'))
                                        ->required()
                                        ->placeholder(__('Example: Blue')),
                                ]),
                        ])
                        ->addActionLabel('افزودن ویژگی جدید')
                        ->reorderable()
                        ->collapsible()
                        ->collapsed()
                        ->itemLabel(fn (array $state): ?string => $state['key'] ?? 'ویژگی جدید')
                        ->columnSpanFull(),
                ]),
        ]);
    }
}

