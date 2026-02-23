<?php

namespace App\Filament\Resources\Customers\Schemas;

use App\Models\Language;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Schema;
use Illuminate\Support\Facades\App;

class CustomerForm
{
    public static function configure(Schema $schema): Schema
    {
        $languages = Language::getAllLanguages();
        $isFarsi   = App::isLocale('fa');

        $toolbarButtons = [
            ['bold', 'italic', 'underline', 'strike', 'subscript', 'superscript', 'link'],
            ['h2', 'h3', 'alignStart', 'alignCenter', 'alignEnd'],
            ['blockquote', 'codeBlock', 'bulletList', 'orderedList'],
            ['table', 'attachFiles'],
            ['undo', 'redo'],
        ];

        $floatingToolbars = [
            'paragraph' => [
                'h2', 'h3', 'bold', 'italic', 'underline', 'strike', 'subscript', 'superscript',
                'alignStart', 'alignCenter', 'alignEnd', 'alignJustify',
            ],
            'heading' => [
                'h1', 'h2', 'h3', 'alignStart', 'alignCenter', 'alignEnd', 'alignJustify',
                'bold', 'italic', 'underline', 'strike',
            ],
            'table' => [
                'tableAddColumnBefore', 'tableAddColumnAfter', 'tableDeleteColumn',
                'tableAddRowBefore', 'tableAddRowAfter', 'tableDeleteRow',
                'tableMergeCells', 'tableSplitCell',
                'tableToggleHeaderRow', 'tableToggleHeaderCell',
                'tableDelete',
            ],
            'attachFiles' => ['alignStart', 'alignCenter', 'alignEnd', 'alignJustify'],
        ];

        $mainLangIndex = $languages->search(fn ($lang) => $lang->name === Language::MAIN_LANG) + 1 ?: 1;

        return $schema
            ->components([
                Tabs::make('Customer Content')
                    ->tabs([
                        // ─── Tab 1: Translations ─────────────────────────────────
                        Tabs\Tab::make(__('Translations'))
                            ->icon('heroicon-o-language')
                            ->schema([
                                Tabs::make('Translations')
                                    ->tabs(
                                        $languages->map(function ($language) use ($toolbarButtons, $floatingToolbars) {
                                            $code   = $language->name;
                                            $isMain = $code === Language::MAIN_LANG;

                                            return Tabs\Tab::make($language->label)
                                                ->icon($language->is_rtl ? 'heroicon-o-arrow-right' : 'heroicon-o-arrow-left')
                                                ->badge($isMain ? __('Main') : null)
                                                ->schema([
                                                    TextInput::make("name.{$code}")
                                                        ->label(__('Customer Name'))
                                                        ->required($isMain)
                                                        ->maxLength(255),

                                                    RichEditor::make("project_description.{$code}")
                                                        ->label(__('Project Description'))
                                                        ->columnSpanFull()
                                                        ->resizableImages()
                                                        ->toolbarButtons($toolbarButtons)
                                                        ->textColors([])
                                                        ->customTextColors()
                                                        ->floatingToolbars($floatingToolbars)
                                                        ->extraInputAttributes(['style' => 'min-height: 160px;']),

                                                    RichEditor::make("testimonial_text.{$code}")
                                                        ->label(__('Testimonial'))
                                                        ->columnSpanFull()
                                                        ->resizableImages()
                                                        ->toolbarButtons($toolbarButtons)
                                                        ->textColors([])
                                                        ->customTextColors()
                                                        ->floatingToolbars($floatingToolbars)
                                                        ->extraInputAttributes(['style' => 'min-height: 140px;']),

                                                    TextInput::make("author_name.{$code}")
                                                        ->label(__('Testimonial Author')),

                                                    TextInput::make("author_position.{$code}")
                                                        ->label(__('Author Position')),
                                                ]);
                                        })->toArray()
                                    )
                                    ->activeTab($mainLangIndex)
                                    ->contained(false)
                                    ->columnSpanFull(),
                            ]),

                        // ─── Tab 2: Customer Info ───────────────────────────────
                        Tabs\Tab::make(__('Customer Info'))
                            ->icon('heroicon-o-building-office-2')
                            ->schema([
                                TextInput::make('industry')
                                    ->label(__('Industry'))
                                    ->maxLength(100),

                                TextInput::make('country')
                                    ->label(__('Country'))
                                    ->maxLength(100),

                                TextInput::make('website_url')
                                    ->label(__('Website URL'))
                                    ->url()
                                    ->maxLength(500),

                                SpatieMediaLibraryFileUpload::make('logo')
                                    ->label(__('Logo'))
                                    ->collection('logo')
                                    ->image()
                                    ->imageEditor()
                                    ->maxSize(2048)
                                    ->downloadable()
                                    ->openable()
                                    ->previewable()
                                    ->helperText(__('Upload logo (Max: 2 MB)'))
                                    ->columnSpanFull(),
                            ])
                            ->columns(2),

                        // ─── Tab 3: Settings ────────────────────────────────────
                        Tabs\Tab::make(__('Settings'))
                            ->icon('heroicon-o-cog-6-tooth')
                            ->schema([
                                Toggle::make('featured')
                                    ->label(__('Featured'))
                                    ->default(false),

                                TextInput::make('order')
                                    ->label(__('Display Order'))
                                    ->numeric()
                                    ->default(0),

                                Select::make('status')
                                    ->label(__('Status'))
                                    ->options([
                                        'draft'     => __('Draft'),
                                        'published' => __('Published'),
                                    ])
                                    ->default('draft')
                                    ->required(),
                            ])
                            ->columns(3),
                    ])
                    ->columnSpanFull(),
            ]);
    }
}
