<?php

namespace App\Filament\Resources\CoreValues\Schemas;

use App\Models\Language;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Schema;
use Illuminate\Support\Facades\App;

class CoreValueForm
{
    public static function configure(Schema $schema): Schema
    {
        $languages = Language::getAllLanguages();
        $isFarsi = App::isLocale('fa');

        $toolbar = [
            ['bold', 'italic', 'underline', 'strike', 'subscript', 'superscript', 'link'],
            ['h2', 'h3', 'alignStart', 'alignCenter', 'alignEnd'],
            ['blockquote', 'codeBlock', 'bulletList', 'orderedList'],
            ['table', 'attachFiles'],
            ['undo', 'redo'],
             ];
        $floating = [
            'paragraph' => [
                'h2', 'h3', 'bold', 'italic', 'underline', 'strike', 'subscript', 'superscript',
                'alignStart', 'alignCenter', 'alignEnd', 'alignJustify'
            ],
            'heading' => [
                'h1', 'h2', 'h3', 'alignStart', 'alignCenter', 'alignEnd', 'alignJustify',
                'bold', 'italic', 'underline', 'strike'
            ],
            'table' => [
                'tableAddColumnBefore', 'tableAddColumnAfter', 'tableDeleteColumn',
                'tableAddRowBefore', 'tableAddRowAfter', 'tableDeleteRow',
                'tableMergeCells', 'tableSplitCell',
                'tableToggleHeaderRow', 'tableToggleHeaderCell',
                'tableDelete',
            ],
            'attachFiles' => [
                'alignStart', 'alignCenter', 'alignEnd', 'alignJustify'
            ],
            ];

        $mainIndex = $languages->search(fn($l) => $l->name === Language::MAIN_LANG) + 1 ?: 1;

        return $schema->components([
            Section::make(__('Translations'))
                ->schema([
                    Tabs::make('translations')
                        ->tabs(
                            $languages->map(fn($lang) => Tabs\Tab::make($lang->label)
                                ->icon($lang->is_rtl ? 'heroicon-o-arrow-right' : 'heroicon-o-arrow-left')
                                ->badge($lang->name === Language::MAIN_LANG ? __('Main') : null)
                                ->schema([
                                    TextInput::make("value_name.{$lang->name}")
                                        ->label(__('Value Name'))
                                        ->required($lang->name === Language::MAIN_LANG)
                                        ->maxLength(255),

                                    RichEditor::make("description.{$lang->name}")
                                        ->label(__('Description'))
                                        ->columnSpanFull()
                                        ->toolbarButtons($toolbar)
                                        ->textColors([])
                                        ->customTextColors()
                                        ->floatingToolbars($floating)
                                        ->extraInputAttributes(['style' => 'min-height: 160px;']),
                                ])
                            )->toArray()
                        )
                        ->activeTab($mainIndex)
                        ->contained(false),
                ])
                ->collapsible(false),

            Section::make(__('Display Settings'))
                ->schema([
                    TextInput::make('icon')
                        ->label(__('Icon'))
                        ->helperText(__('icon name e.g. heroicon-o-heart')),

                    TextInput::make('order')
                        ->label(__('Order'))
                        ->numeric()
                        ->default(0),

                    Select::make('status')
                        ->label(__('Status'))
                        ->options(['draft' => __('Draft'), 'published' => __('Published')])
                        ->default('draft')
                        ->required(),
                ])
                ->columns(3),
        ]);
    }
}
