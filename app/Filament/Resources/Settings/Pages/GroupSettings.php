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

    // Create / Delete modal states
    public array $newSetting = ['name' => '', 'label' => '', 'type' => ''];
    public string $deleteSettingName = '';

    public function mount(string $group): void
    {
        abort_unless(array_key_exists($group, Setting::getAllGroups()), 404);

        $this->group = $group;

        // Get all settings for this group
        $settings = Setting::query()->where('group', $group)->get();

        // Build initial form data
        $values = [];
        foreach ($settings as $setting) {
            $fieldName = Str::replace('.', '_', $setting->name);

            if (in_array($setting->type, [Setting::TYPE_IMAGE, Setting::TYPE_VIDEO])) {
                // For media fields, we don't set initial value - the component handles it
                $values[$fieldName] = null;
            } else {
                // Get raw value from database, not the accessor
                $values[$fieldName] = $setting->getRawOriginal('value');
            }
        }

        $this->form->fill($values);
    }

    public function form(Schema $schema): Schema
    {
        $components = [];

        $settings = Setting::query()
            ->where('group', $this->group)
            ->get();

        foreach ($settings as $setting) {
            $fieldName = Str::replace('.', '_', $setting->name);

            $component = match ($setting->type) {
                Setting::TYPE_TEXT => Forms\Components\TextInput::make($fieldName)
                    ->maxLength(191)
                    ->default(null),

                Setting::TYPE_NUMBER => Forms\Components\TextInput::make($fieldName)
                    ->numeric()
                    ->step('any')
                    ->inputMode('decimal')
                    ->default(null),

                Setting::TYPE_TOGGLE => Forms\Components\Toggle::make($fieldName)
                    ->helperText(__('On / Off'))
                    ->default(false),

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
                            'h2', 'h3', 'bold', 'italic', 'underline', 'strike', 'subscript', 'superscript','alignStart', 'alignCenter', 'alignEnd', 'alignJustify'
                        ],
                        'heading' => ['h1', 'h2', 'h3','alignStart', 'alignCenter', 'alignEnd', 'alignJustify','bold', 'italic', 'underline', 'strike'],
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
                    ->extraInputAttributes(['style' => 'min-height: 140px;'])
                    ->default(null),

                Setting::TYPE_IMAGE => SpatieMediaLibraryFileUpload::make($fieldName)
                    ->collection('setting_files')
                    ->model($setting)
                    ->image()
                    ->imageEditor()
                    ->disk('public')
                    ->directory('settings/' . $this->group)
                    ->visibility('public')
                    ->maxSize(5120)
                    ->columnSpan('full')
                    ->helperText(__('Maximum size: 5 MB')),

                Setting::TYPE_VIDEO => SpatieMediaLibraryFileUpload::make($fieldName)
                    ->collection('setting_files')
                    ->model($setting)
                    ->acceptedFileTypes(['video/mp4', 'video/webm', 'video/ogg'])
                    ->disk('public')
                    ->directory('settings/' . $this->group)
                    ->visibility('public')
                    ->maxSize(20480)
                    ->columnSpan('full')
                    ->helperText(__('Accepted formats: MP4, WebM, OGG - Maximum size: 20 MB')),

                default => Forms\Components\TextInput::make($fieldName)
                    ->default(null),
            };

            $component->label($setting->label);

            $components[] = $component;
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
            // First try: use the form key as-is (hero_title)
            $setting = Setting::query()
                ->where('group', $this->group)
                ->where('name', $key)
                ->first();

            // If not found, fallback to dotted version (hero.title)
            if (!$setting) {
                $dottedName = str_replace('_', '.', $key);
                $setting = Setting::query()
                    ->where('group', $this->group)
                    ->where('name', $dottedName)
                    ->first();
            }

            if (!$setting) {
                continue;
            }

            if (in_array($setting->type, [Setting::TYPE_IMAGE, Setting::TYPE_VIDEO])) {
                $setting->refresh();
                $media = $setting->getFirstMedia('setting_files');
                $setting->value = $media ? $media->getFullUrl() : null;
            } else {
                // Non-media fields: let the mutator handle formatting / null / toggle
                $setting->value = $value;
            }

            $setting->save();

            // Optional: force refresh to ensure latest data
            $setting->refresh();
        }

        $this->resetErrorBag();
        $this->resetValidation();

        Notification::make()
            ->success()
            ->title(__('Settings updated successfully'))
            ->send();

        // Rebuild form with fresh data from DB
        $this->mount($this->group);
    }

    // Create new key in this group
    public function createSetting(): void
    {
        $this->validate([
            'newSetting.name'  => ['required', 'regex:/^[a-z0-9_.]+$/', 'unique:settings,name,NULL,id,group,' . $this->group],
            'newSetting.label' => 'required|max:191',
            'newSetting.type'  => 'required|in:text,number,textarea,image,video,toggle',
        ]);

        Setting::create([
            'group' => $this->group,
            'name'  => $this->newSetting['name'],
            'label' => $this->newSetting['label'],
            'type'  => $this->newSetting['type'],
            'value' => null,
        ]);

        $this->reset('newSetting');

        Notification::make()
            ->success()
            ->title(__('New key created'))
            ->send();

        $this->mount($this->group);
    }

    // Delete existing key
    public function deleteSetting(): void
    {
        $this->validate(['deleteSettingName' => 'required']);

        $setting = Setting::query()->where(['group' => $this->group, 'name' => $this->deleteSettingName])->first();

        $setting?->delete();

        $this->reset('deleteSettingName');

        Notification::make()
            ->success()
            ->title(__('Key deleted'))
            ->send();

        $this->mount($this->group);
    }
    public function getBreadcrumbs(): array
    {
        $groupTitle = Setting::getAllGroups()[$this->group]['title'] ?? $this->group;

        return [
            SettingResource::getUrl('index') => __('Settings'),
            '#' => __($groupTitle),
        ];
    }
    public function getHeading(): string|\Illuminate\Contracts\Support\Htmlable|null
    {
        $groupTitle = Setting::getAllGroups()[$this->group]['title'] ?? $this->group;
        return __('Edit Settings') . ' – ' . __($groupTitle);
    }
}
