<?php

namespace App\Filament\Resources\Accreditations\Schemas;

use App\Models\Language;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Schema;
use Illuminate\Support\Facades\App;

class AccreditationForm
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
                Tabs::make('Accreditation Content')
                    ->tabs([
                        // ─── Tab 1: Translations ────────────────────────────────
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
                                                    TextInput::make("organization_name.{$code}")
                                                        ->label(__('Organization Name'))
                                                        ->required($isMain)
                                                        ->maxLength(255),

                                                    TextInput::make("accreditation_type.{$code}")
                                                        ->label(__('Accreditation Type'))
                                                        ->maxLength(100),

                                                    RichEditor::make("description.{$code}")
                                                        ->label(__('Description'))
                                                        ->columnSpanFull()
                                                        ->resizableImages()
                                                        ->toolbarButtons($toolbarButtons)
                                                        ->textColors([])
                                                        ->customTextColors()
                                                        ->floatingToolbars($floatingToolbars)
                                                        ->extraInputAttributes(['style' => 'min-height: 180px;']),
                                                ]);
                                        })->toArray()
                                    )
                                    ->activeTab($mainLangIndex)
                                    ->contained(false)
                                    ->columnSpanFull(),
                            ]),

                        // ─── Tab 2: Membership Details ──────────────────────────
                        Tabs\Tab::make(__('Membership Details'))
                            ->icon('heroicon-o-identification')
                            ->schema([
                                TextInput::make('membership_number')
                                    ->label(__('Membership Number'))
                                    ->maxLength(100),

                                DatePicker::make('member_since')
                                    ->label(__('Member Since'))
                                    ->native(false)
                                    ->displayFormat($isFarsi ? 'j F Y' : 'M j, Y')
                                    ->closeOnDateSelection()
                                    ->helperText($isFarsi
                                        ? __('Select the date in Jalali calendar')
                                        : __('Select the date')
                                    )
                                    ->when($isFarsi, fn (DatePicker $picker) => $picker->jalali()),

                                DatePicker::make('start_date')
                                    ->label(__('Start Date'))
                                    ->native(false)
                                    ->displayFormat($isFarsi ? 'j F Y' : 'M j, Y')
                                    ->closeOnDateSelection()
                                    ->helperText($isFarsi
                                        ? __('Select the date in Jalali calendar')
                                        : __('Select the date')
                                    )
                                    ->when($isFarsi, fn (DatePicker $picker) => $picker->jalali()),

                                DatePicker::make('end_date')
                                    ->label(__('End Date'))
                                    ->native(false)
                                    ->displayFormat($isFarsi ? 'j F Y' : 'M j, Y')
                                    ->closeOnDateSelection()
                                    ->helperText($isFarsi
                                        ? __('Select the date in Jalali calendar')
                                        : __('Select the date')
                                    )
                                    ->after('start_date')
                                    ->when($isFarsi, fn (DatePicker $picker) => $picker->jalali()),

                                TextInput::make('verification_url')
                                    ->label(__('Verification URL'))
                                    ->url()
                                    ->maxLength(500)
                                    ->columnSpanFull(),
                            ])
                            ->columns(2),

                        // ─── Tab 3: Files ───────────────────────────────────────
                        Tabs\Tab::make(__('Files'))
                            ->icon('heroicon-o-document-arrow-down')
                            ->schema([
                                SpatieMediaLibraryFileUpload::make('logo')
                                    ->label(__('Logo'))
                                    ->collection('logo')
                                    ->image()
                                    ->imageEditor()
                                    ->maxSize(2048)
                                    ->downloadable()
                                    ->openable()
                                    ->previewable()
                                    ->helperText(__('Upload logo (Max: 2 MB)')),

                                SpatieMediaLibraryFileUpload::make('certificate')
                                    ->label(__('Certificate PDF'))
                                    ->collection('certificate')
                                    ->acceptedFileTypes(['application/pdf'])
                                    ->maxSize(10240)
                                    ->downloadable()
                                    ->openable()
                                    ->helperText(__('Upload certificate PDF (Max: 10 MB)')),
                            ])
                            ->columns(2),

                        // ─── Tab 4: Settings ────────────────────────────────────
                        Tabs\Tab::make(__('Settings'))
                            ->icon('heroicon-o-cog-6-tooth')
                            ->schema([
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
                            ->columns(2),
                    ])
                    ->columnSpanFull(),
            ]);
    }
}
