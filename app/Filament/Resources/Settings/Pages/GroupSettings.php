<?php

namespace App\Filament\Resources\Settings\Pages;

use App\Filament\Resources\Settings\SettingResource;
use App\Models\Setting;
use Filament\Forms;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\Page;
use Filament\Schemas\Schema;
use Illuminate\Support\Str;

class GroupSettings extends Page implements HasForms
{
    use InteractsWithForms;

    protected static string $resource = SettingResource::class;

    protected string $view = 'filament.resources.settings.pages.group-settings';

    public string $group;

    public ?array $data = [];

    public function mount(string $group): void
    {
        abort_unless(array_key_exists($group, Setting::getAllGroups()), 404);

        $this->group = $group;

        $values = Setting::query()
            ->where('group', $group)
            ->pluck('value', 'name')
            ->mapWithKeys(fn($v, $k) => [Str::replace('.', '_', $k) => $v])
            ->toArray();

        $this->form->fill($values);
    }

    public function form(Schema $schema): Schema
    {
        $settingsByType = Setting::query()
            ->where('group', $this->group)
            ->get()
            ->groupBy('type');

        $components = [];

        foreach ($settingsByType as $type => $settings) {
            foreach ($settings as $setting) {
                $fieldName = Str::replace('.', '_', $setting->name);

                $component = match ($type) {
                    Setting::TYPE_TEXT => Forms\Components\TextInput::make($fieldName)
                        ->maxLength(191),

                    Setting::TYPE_NUMBER => Forms\Components\TextInput::make($fieldName)
                        ->numeric()
                        ->minValue(0),

                    Setting::TYPE_TOGGLE => Forms\Components\Toggle::make($fieldName)
                        ->helperText('روشن / خاموش'),

                    Setting::TYPE_TEXTAREA => Forms\Components\RichEditor::make($fieldName)
                        ->columnSpan(2)
                        ->resizableImages()
                        ->toolbarButtons([
                            ['bold', 'italic', 'underline', 'strike', 'subscript', 'superscript', 'link'],
                            ['h2', 'h3', 'alignStart', 'alignCenter', 'alignEnd'],
                            ['blockquote', 'codeBlock', 'bulletList', 'orderedList'],
                            ['table', 'attachFiles'],
                            ['undo', 'redo'],
                        ])
                        ->textColors([])
                        ->customTextColors()
                        ->floatingToolbars([
                            'paragraph' => [
                                'bold', 'italic', 'underline', 'strike', 'subscript', 'superscript','alignStart', 'alignCenter', 'alignEnd', 'alignJustify'
                            ],
                            'heading' => ['h1', 'h2', 'h3'],
                            'table' => [
                                'tableAddColumnBefore', 'tableAddColumnAfter', 'tableDeleteColumn',
                                'tableAddRowBefore', 'tableAddRowAfter', 'tableDeleteRow',
                                'tableMergeCells', 'tableSplitCell',
                                'tableToggleHeaderRow', 'tableToggleHeaderCell',
                                'tableDelete',
                            ],
                            'attachFiles' => [
                                'alignStart', 'alignCenter', 'alignEnd', 'alignJustify'
                            ]
                        ])
                        ->extraInputAttributes(['style' => 'min-height: 140px;']),

                    Setting::TYPE_IMAGE => SpatieMediaLibraryFileUpload::make($fieldName)
                        ->collection('setting_files')
                        ->image()
                        ->imageEditor()
                        ->disk('public')
                        ->directory('settings')
                        ->columnSpan('full'),

                    Setting::TYPE_VIDEO => SpatieMediaLibraryFileUpload::make($fieldName)
                        ->collection('setting_files')
                        ->acceptedFileTypes(['video/mp4', 'video/webm', 'video/ogg'])
                        ->disk('public')
                        ->directory('settings')
                        ->columnSpan('full'),

                    default => Forms\Components\TextInput::make($fieldName),
                };

                $component->label($setting->label);

                // Full width for large fields
                if (in_array($type, [Setting::TYPE_TEXTAREA, Setting::TYPE_IMAGE, Setting::TYPE_VIDEO])) {
                    $component->columnSpan('full');
                }

                $components[] = $component;
            }
        }

        return $schema
            ->schema($components)
            ->statePath('data')
            ->columns([
                'default' => 1,
                'md'      => 2,
            ]);
    }

    public function save(): void
    {
        $state = $this->form->getState();

        foreach ($state as $key => $value) {
            $originalName = Str::replace('_', '.', $key);

            $setting = Setting::firstWhere([
                'group' => $this->group,
                'name'  => $originalName,
            ]);

            if (!$setting) {
                continue;
            }

            // Only update value for non-media types
            if (!in_array($setting->type, [Setting::TYPE_IMAGE, Setting::TYPE_VIDEO])) {
                $setting->update(['value' => $value]);
            }

            // Optional: if you want to store URL in value column too:
            /*
            if (in_array($setting->type, [Setting::TYPE_IMAGE, Setting::TYPE_VIDEO])) {
                $setting->refresh();
                $media = $setting->getFirstMedia('setting_files');
                $setting->update(['value' => $media?->getFullUrl()]);
            }
            */
        }

        Notification::make()
            ->success()
            ->title('تنظیمات با موفقیت به‌روزرسانی شد')
            ->send();
    }
}
