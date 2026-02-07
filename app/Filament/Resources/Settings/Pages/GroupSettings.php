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
                $values[$fieldName] = $setting->value;
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
                    ->model($setting) // THIS IS CRITICAL - links to the model instance
                    ->image()
                    ->imageEditor()
                    ->disk('public')
                    ->directory('settings/' . $this->group)
                    ->visibility('public')
                    ->maxSize(5120) // 5MB
                    ->columnSpan('full')
                    ->helperText('حداکثر حجم: 5 مگابایت'),

                Setting::TYPE_VIDEO => SpatieMediaLibraryFileUpload::make($fieldName)
                    ->collection('setting_files')
                    ->model($setting) // THIS IS CRITICAL - links to the model instance
                    ->acceptedFileTypes(['video/mp4', 'video/webm', 'video/ogg'])
                    ->disk('public')
                    ->directory('settings/' . $this->group)
                    ->visibility('public')
                    ->maxSize(20480) // 20MB
                    ->columnSpan('full')
                    ->helperText('فرمت‌های قابل قبول: MP4, WebM, OGG - حداکثر حجم: 20 مگابایت'),

                default => Forms\Components\TextInput::make($fieldName),
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
            $originalName = Str::replace('_', '.', $key);

            $setting = Setting::firstWhere([
                'group' => $this->group,
                'name'  => $originalName,
            ]);

            if (!$setting) continue;

            // For media types: the SpatieMediaLibraryFileUpload component
            // has already handled the upload via the ->model() binding
            // We just need to sync the URL to the value column
            if (in_array($setting->type, [Setting::TYPE_IMAGE, Setting::TYPE_VIDEO])) {
                $setting->refresh(); // Refresh to get the latest media
                $media = $setting->getFirstMedia('setting_files');
                $setting->update(['value' => $media ? $media->getFullUrl() : null]);
            } else {
                // For non-media types, just update the value
                $setting->update(['value' => $value]);
            }
        }

        Notification::make()
            ->success()
            ->title('تنظیمات با موفقیت به‌روزرسانی شد')
            ->send();

        // Refresh the form to show updated media
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

        Notification::make()->success()->title('کلید جدید ایجاد شد')->send();

        // Remount to rebuild the form with the new setting
        $this->mount($this->group);
    }

    // Delete existing key
    public function deleteSetting(): void
    {
        $this->validate(['deleteSettingName' => 'required']);

        $setting = Setting::query()->where(['group' => $this->group, 'name' => $this->deleteSettingName])->first();

        $setting?->delete();

        $this->reset('deleteSettingName');

        Notification::make()->success()->title('کلید حذف شد')->send();

        // Remount to rebuild the form without the deleted setting
        $this->mount($this->group);
    }
}
