<?php

namespace App\Filament\Resources\Settings\Pages;

use App\Filament\Resources\Settings\SettingResource;
use App\Models\Setting;
use Filament\Resources\Pages\ListRecords;

class ListSettings extends ListRecords
{
    protected static string $resource = SettingResource::class;

    // Override default table view → use fully custom Blade
    protected string $view = 'filament.resources.settings.pages.list-settings';

    // Allow full width for the card grid
    protected string|\Filament\Support\Enums\Width|null $maxContentWidth = 'full';

    public function getTitle(): string
    {
        return __('Settings');
    }

    public function getGroupsProperty()
    {
        return collect(Setting::getAllGroups());
    }
}
