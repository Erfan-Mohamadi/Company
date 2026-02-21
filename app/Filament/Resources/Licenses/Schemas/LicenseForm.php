<?php

namespace App\Filament\Resources\Licenses\Schemas;


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

class LicenseForm
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
                Tabs::make('License Content')
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
                                                    TextInput::make("license_name.{$code}")
                                                        ->label(__('License Name'))
                                                        ->required($isMain)
                                                        ->maxLength(255),

                                                    TextInput::make("issuing_authority.{$code}")
                                                        ->label(__('Issuing Authority'))
                                                        ->maxLength(255),

                                                    RichEditor::make("description.{$code}")
                                                        ->label(__('Description'))
                                                        ->columnSpanFull()
                                                        ->resizableImages()
                                                        ->toolbarButtons($toolbarButtons)
                                                        ->textColors([])
                                                        ->customTextColors()
                                                        ->floatingToolbars($floatingToolbars)
                                                        ->extraInputAttributes(['style' => 'min-height: 160px;']),
                                                ]);
                                        })->toArray()
                                    )
                                    ->activeTab($mainLangIndex)
                                    ->contained(false)
                                    ->columnSpanFull(),
                            ]),

                        // ─── Tab 2: License Details ─────────────────────────────
                        Tabs\Tab::make(__('License Details'))
                            ->icon('heroicon-o-identification')
                            ->schema([
                                TextInput::make('license_number')
                                    ->label(__('License Number'))
                                    ->unique(ignoreRecord: true)
                                    ->maxLength(100),

                                Select::make('license_type')
                                    ->label(__('License Type'))
                                    ->options([
                                        'trade'        => __('Trade'),
                                        'professional' => __('Professional'),
                                        'operating'    => __('Operating'),
                                        'import'       => __('Import / Export'),
                                        'other'        => __('Other'),
                                    ]),

                                DatePicker::make('issue_date')
                                    ->label(__('Issue Date'))
                                    ->native(false)
                                    ->displayFormat($isFarsi ? 'j F Y' : 'M j, Y')
                                    ->closeOnDateSelection()
                                    ->helperText($isFarsi
                                        ? __('Select the date in Jalali calendar')
                                        : __('Select the date')
                                    )
                                    ->when($isFarsi, fn (DatePicker $picker) => $picker->jalali()),

                                DatePicker::make('expiry_date')
                                    ->label(__('Expiry Date'))
                                    ->native(false)
                                    ->displayFormat($isFarsi ? 'j F Y' : 'M j, Y')
                                    ->closeOnDateSelection()
                                    ->helperText($isFarsi
                                        ? __('Select the date in Jalali calendar')
                                        : __('Select the date')
                                    )
                                    ->after('issue_date')
                                    ->when($isFarsi, fn (DatePicker $picker) => $picker->jalali()),
                            ])
                            ->columns(2),

                        // ─── Tab 3: File ────────────────────────────────────────
                        Tabs\Tab::make(__('File'))
                            ->icon('heroicon-o-document-arrow-down')
                            ->schema([
                                SpatieMediaLibraryFileUpload::make('license_file')
                                    ->label(__('License PDF'))
                                    ->collection('license_file')
                                    ->acceptedFileTypes(['application/pdf'])
                                    ->maxSize(10240)
                                    ->downloadable()
                                    ->openable()
                                    ->columnSpanFull()
                                    ->helperText(__('Upload license PDF (Maximum size: 10 MB)')),
                            ]),

                        // ─── Tab 4: Display Settings ────────────────────────────
                        Tabs\Tab::make(__('Display Settings'))
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
