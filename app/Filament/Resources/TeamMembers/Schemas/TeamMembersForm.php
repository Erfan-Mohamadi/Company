<?php

namespace App\Filament\Resources\TeamMembers\Schemas;

use App\Models\Language;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Schema;
use Illuminate\Support\Facades\App;

class TeamMembersForm
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
                Tabs::make('Team Member Content')
                    ->tabs([

                        // ─── Tab 1: Translations ─────────────────────────────────
                        Tabs\Tab::make(__('Translations'))
                            ->icon('heroicon-o-language')
                            ->schema([
                                Tabs::make('Translations')
                                    ->tabs(
                                        $languages->map(function ($language) use ($isFarsi, $toolbarButtons, $floatingToolbars) {
                                            $code   = $language->name;
                                            $isMain = $code === Language::MAIN_LANG;

                                            return Tabs\Tab::make($language->label)
                                                ->icon($language->is_rtl ? 'heroicon-o-arrow-right' : 'heroicon-o-arrow-left')
                                                ->badge($isMain ? __('Main') : null)
                                                ->schema([

                                                    // ── Core fields ──────────────────────────────────────
                                                    TextInput::make("name.{$code}")
                                                        ->label(__('Name'))
                                                        ->required($isMain)
                                                        ->maxLength(255),

                                                    TextInput::make("position.{$code}")
                                                        ->label(__('Position'))
                                                        ->required($isMain)
                                                        ->maxLength(255),

                                                    // ── Bio (rich) ───────────────────────────────────────
                                                    RichEditor::make("bio.{$code}")
                                                        ->label(__('Biography'))
                                                        ->columnSpanFull()
                                                        ->resizableImages()
                                                        ->toolbarButtons($toolbarButtons)
                                                        ->textColors([])
                                                        ->customTextColors()
                                                        ->floatingToolbars($floatingToolbars)
                                                        ->extraInputAttributes(['style' => 'min-height: 200px;']),

                                                    // ── Education ────────────────────────────────────────
                                                    Repeater::make("education.{$code}")
                                                        ->label(__('Education'))
                                                        ->schema([
                                                            TextInput::make('degree')
                                                                ->label(__('Degree'))
                                                                ->maxLength(255),

                                                            TextInput::make('institution')
                                                                ->label(__('Institution'))
                                                                ->maxLength(255),

                                                            DatePicker::make('year')
                                                                ->label(__('Year'))
                                                                ->format('Y')
                                                                ->displayFormat('Y')
                                                                ->native(false)
                                                                ->closeOnDateSelection()
                                                                ->helperText(
                                                                    $isFarsi
                                                                        ? __('Select the year in Jalali calendar')
                                                                        : __('Select the year')
                                                                )
                                                                ->when($isFarsi, fn (DatePicker $picker) => $picker->jalali()),
                                                        ])
                                                        ->columns(3)
                                                        ->columnSpanFull(),

                                                    // ── Experience ───────────────────────────────────────
                                                    Repeater::make("experience.{$code}")
                                                        ->label(__('Experience'))
                                                        ->schema([
                                                            TextInput::make('role')
                                                                ->label(__('Role'))
                                                                ->maxLength(255),

                                                            TextInput::make('organization')
                                                                ->label(__('Organization'))
                                                                ->maxLength(255),

                                                            TextInput::make('duration')
                                                                ->label(__('Duration'))
                                                                ->maxLength(100),
                                                        ])
                                                        ->columns(3)
                                                        ->columnSpanFull(),

                                                ]);
                                        })->toArray()
                                    )
                                    ->activeTab($mainLangIndex)
                                    ->contained(false)
                                    ->columnSpanFull(),
                            ]),

                        // ─── Tab 2: Profile Photo ────────────────────────────────
                        Tabs\Tab::make(__('Profile Photo'))
                            ->icon('heroicon-o-user-circle')
                            ->schema([
                                SpatieMediaLibraryFileUpload::make('image')
                                    ->label(__('Photo'))
                                    ->collection('image')
                                    ->image()
                                    ->imageEditor()
                                    ->imageCropAspectRatio('1:1')
                                    ->maxSize(5120)
                                    ->downloadable()
                                    ->openable()
                                    ->previewable()
                                    ->columnSpanFull()
                                    ->helperText(__('Upload profile photo (Maximum size: 5 MB)')),
                            ]),

                        // ─── Tab 3: Contact & Social ─────────────────────────────
                        Tabs\Tab::make(__('Contact & Social'))
                            ->icon('heroicon-o-envelope')
                            ->schema([
                                TextInput::make('email')
                                    ->label(__('Email'))
                                    ->email()
                                    ->maxLength(255),

                                TextInput::make('phone')
                                    ->label(__('Phone'))
                                    ->tel()
                                    ->maxLength(50),

                                TextInput::make('linkedin_url')
                                    ->label(__('LinkedIn URL'))
                                    ->url()
                                    ->maxLength(255)
                                    ->prefix('https://'),

                                TextInput::make('twitter_url')
                                    ->label(__('Twitter / X URL'))
                                    ->url()
                                    ->maxLength(255)
                                    ->prefix('https://'),

                                TextInput::make('facebook_url')
                                    ->label(__('Facebook URL'))
                                    ->url()
                                    ->maxLength(255)
                                    ->prefix('https://'),
                            ])
                            ->columns(2),

                        // ─── Tab 4: Skills ────────────────────────────────────────
                        // skills is a non-translatable JSON column: [{name, level}]
                        Tabs\Tab::make(__('Skills'))
                            ->icon('heroicon-o-bolt')
                            ->schema([
                                Repeater::make('skills')
                                    ->label(__('Skills'))
                                    ->schema([
                                        TextInput::make('name')
                                            ->label(__('Skill'))
                                            ->maxLength(100)
                                            ->required(),

                                        Select::make('level')
                                            ->label(__('Level'))
                                            ->options([
                                                'beginner'     => __('Beginner'),
                                                'intermediate' => __('Intermediate'),
                                                'advanced'     => __('Advanced'),
                                                'expert'       => __('Expert'),
                                            ]),
                                    ])
                                    ->columns(2)
                                    ->columnSpanFull(),
                            ]),

                        // ─── Tab 5: Display Settings ──────────────────────────────
                        Tabs\Tab::make(__('Display Settings'))
                            ->icon('heroicon-o-cog-6-tooth')
                            ->schema([
                                Select::make('department_id')
                                    ->label(__('Department'))
                                    ->relationship('department', 'name')
                                    ->getOptionLabelFromRecordUsing(fn ($record) => $record->getTranslation('name', app()->getLocale()))
                                    ->searchable()
                                    ->preload(),

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
