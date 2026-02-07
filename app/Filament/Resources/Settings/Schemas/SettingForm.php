<?php

namespace App\Filament\Resources\Settings\Schemas;

use App\Models\Setting;
use Filament\Forms;
use Filament\Schemas\Components\Form;
use Illuminate\Support\Str;

class SettingForm
{
    public static function make(Form $form, string $group): Form
    {
        $settingsByType = Setting::query()->where('group', $group)
            ->get()
            ->groupBy('type');

        $schema = [];

        foreach ($settingsByType as $type => $settings) {
            foreach ($settings as $setting) {
                $fieldName = Str::replace('.', '_', $setting->name);

                $component = match ($type) {
                    Setting::TYPE_TEXT => Forms\Components\TextInput::make($fieldName)
                        ->label($setting->label)
                        ->maxLength(191),

                    Setting::TYPE_NUMBER => Forms\Components\TextInput::make($fieldName)
                        ->label($setting->label)
                        ->numeric()
                        ->minValue(0),

                    Setting::TYPE_TEXTAREA => Forms\Components\Textarea::make($fieldName)
                        ->label($setting->label)
                        ->rows(6),

                    Setting::TYPE_IMAGE => Forms\Components\FileUpload::make($fieldName)
                        ->label($setting->label)
                        ->image()
                        ->disk('public')
                        ->imageEditor()
                        ->imageCropAspectRatio('16:9') // optional
                        ->afterStateHydrated(function ($state) use ($setting) {
                            return $setting->file['url'] ?? null;
                        })
                        ->dehydrated(fn ($state): bool => $state instanceof \Illuminate\Http\UploadedFile),

                    Setting::TYPE_VIDEO => Forms\Components\FileUpload::make($fieldName)
                        ->label($setting->label)
                        ->acceptedFileTypes(['video/mp4', 'video/webm', 'video/ogg'])
                        ->afterStateHydrated(function ($state) use ($setting) {
                            return $setting->file['url'] ?? null;
                        })
                        ->dehydrated(fn ($state): bool => $state instanceof \Illuminate\Http\UploadedFile),

                    default => Forms\Components\TextInput::make($fieldName)
                        ->label($setting->label),
                };

                // Make large fields span full width
                if (in_array($type, [Setting::TYPE_TEXTAREA, Setting::TYPE_IMAGE, Setting::TYPE_VIDEO])) {
                    $component->columnSpan('full');
                }

                $schema[] = $component;
            }
        }

        return $form
            ->schema($schema)
            ->statePath('data')
            ->columns(2); // 2 columns for most fields, full for large ones
    }
}
