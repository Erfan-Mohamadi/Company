@php
    use App\Filament\Resources\Settings\SettingResource;
    use App\Models\Setting;
@endphp

<x-filament-panels::page>

    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
                <div class="d-flex gap-2">
                    <x-filament::button tag="a" href="{{ SettingResource::getUrl('index') }}" color="gray">
                        {{ __('Back') }}
                    </x-filament::button>
                    <x-filament::button color="danger" wire:click="$dispatch('open-modal', { id: 'delete-setting-modal' })">
                        {{ __('Delete Key') }}
                    </x-filament::button>

                    <x-filament::button color="primary" wire:click="$dispatch('open-modal', { id: 'create-setting-modal' })">
                        {{ __('Create New Key') }}
                    </x-filament::button>
                </div>
            </div>
        </div>
    </div>

    <x-filament::card>
        <x-slot name="heading">{{ __('Edit Settings') }} - {{ $this->group }}</x-slot>

        <form wire:submit="save">
            <div class="row">
                {{ $this->form }}

                <div class="col-12 text-center mt-5" style="margin-top: 0.5rem">
                    <x-filament::button type="submit" color="warning" size="lg">
                        {{ __('Update') }}
                    </x-filament::button>
                </div>
            </div>
        </form>
    </x-filament::card>

    {{-- CREATE SETTING MODAL – Filament v4 safe version --}}
    {{-- CREATE SETTING MODAL – With valid icons --}}
    <x-filament::modal id="create-setting-modal" width="2xl">
        <x-slot name="heading">
            <div class="flex items-center gap-3">
                <x-filament::icon icon="heroicon-o-plus-circle" class="w-8 h-8 text-primary-600 dark:text-primary-400" />
                <div>
                    <h2 class="text-xl font-bold text-gray-950 dark:text-white">{{ __('Create New Key') }}</h2>
                    <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                        {{ __('New key for group') }} «{{ Setting::getAllGroups()[$this->group]['title'] ?? $this->group }}»
                    </p>
                </div>
            </div>
        </x-slot>

        <form wire:submit="createSetting" class="space-y-6 mt-6">

            {{-- Key name --}}
            <div style="margin-bottom: 1rem">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-1.5">
                    {{ __('Key Name (English)') }} <span class="text-red-500">*</span>
                </label>

                <x-filament::input.wrapper>
                    <x-filament::input
                        type="text"
                        id="newSetting.name"
                        wire:model.live.debounce.500ms="newSetting.name"
                        placeholder="{{ __('Example: site_title, telegram_link') }}"
                        dir="ltr"
                    />
                </x-filament::input.wrapper>

                <p class="mt-1.5 text-xs text-gray-500 dark:text-gray-400">
                    {{ __('Only lowercase English letters, numbers, _ and .') }}
                </p>

                @error('newSetting.name')
                <p class="mt-2 text-sm text-red-600 dark:text-red-400 flex items-center gap-2" style="display: inline-flex ; color: red">
                    <x-filament::icon icon="heroicon-s-exclamation-circle" class="w-4 h-4 text-red-600 flex-shrink-0" />
                    {{ $message }}
                </p>
                @enderror
            </div>

            {{-- Label --}}
            <div style="margin-bottom: 1rem">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-1.5">
                    {{ __('Label') }} <span class="text-red-500">*</span>
                </label>

                <x-filament::input.wrapper>
                    <x-filament::input
                        type="text"
                        id="newSetting.label"
                        wire:model="newSetting.label"
                        placeholder="{{ __('Example: Site Title') }}"
                    />
                </x-filament::input.wrapper>

                <p class="mt-1.5 text-xs text-gray-500 dark:text-gray-400">
                    {{ __('This text will be displayed in the form') }}
                </p>

                @error('newSetting.label')
                <p class="mt-2 text-sm text-red-600 dark:text-red-400 flex items-center gap-2" style="display: inline-flex; color: red">
                    <x-filament::icon icon="heroicon-s-exclamation-circle" class="w-4 h-4 text-red-600 flex-shrink-0" />
                    {{ $message }}
                </p>
                @enderror
            </div>

            {{-- Type --}}
            <div style="margin-bottom: 1rem">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-1.5">
                    {{ __('Field Type') }} <span class="text-red-500">*</span>
                </label>

                <x-filament::input.wrapper>
                    <x-filament::input.select
                        id="newSetting.type"
                        wire:model="newSetting.type"
                    >
                        <option value="" disabled selected>{{ __('Select a type...') }}</option>
                        @foreach(Setting::getAllTypes() as $key => $label)
                            <option value="{{ $key }}">{{ __($label) }}</option>
                        @endforeach
                    </x-filament::input.select>
                </x-filament::input.wrapper>

                @error('newSetting.type')
                <p class="mt-2 text-sm text-red-600 dark:text-red-400 flex items-center gap-2" style="display: inline-flex; color: red">
                    <x-filament::icon icon="heroicon-s-exclamation-circle" class="w-4 h-4 text-red-600 flex-shrink-0" />
                    {{ __($message) }}
                </p>
                @enderror
            </div>

            {{-- Hint --}}
            <div class="rounded-xl bg-blue-50 dark:bg-blue-950/30 border border-blue-200 dark:border-blue-800 p-5 text-sm text-blue-800 dark:text-blue-200">
                <p class="font-medium">{{ __('Important Note') }}</p>
                <p class="mt-1">{{ __('After creating a new key, its value will appear in the main page form and you can edit it.') }}</p>
            </div>

            {{-- Buttons --}}
            <div class="flex justify-end gap-3 pt-6 border-t border-gray-200 dark:border-gray-700">
                <x-filament::button
                    type="button"
                    color="gray"
                    wire:click="$dispatch('close-modal', { id: 'create-setting-modal' })"
                >
                    {{ __('Cancel') }}
                </x-filament::button>

                <x-filament::button
                    type="submit"
                    color="primary"
                    icon="heroicon-m-check"
                    wire:loading.attr="disabled"
                    wire:target="createSetting"
                >
                    <span wire:loading.remove wire:target="createSetting">{{ __('Create Key') }}</span>
                    <span wire:loading wire:target="createSetting" class="flex items-center gap-2">
                    <x-filament::loading-indicator class="h-4 w-4" />
                    {{ __('Creating...') }}
                </span>
                </x-filament::button>
            </div>
        </form>
    </x-filament::modal>

    {{-- DELETE MODAL --}}
    <x-filament::modal
        id="delete-setting-modal"
        width="xl"
    >
        <x-slot name="heading">
            <div class="flex items-center gap-3">
                <div class="p-3 bg-gradient-to-br from-red-500 to-red-600 rounded-xl">
                    <x-filament::icon icon="heroicon-o-exclamation-triangle" class="w-6 h-6 text-white" />
                </div>
                <div>
                    <h3 class="text-xl font-bold">{{ __('Delete Key') }}</h3>
                    <p class="text-sm text-gray-500 dark:text-gray-400">{{ __('This action is irreversible!') }}</p>
                </div>
            </div>
        </x-slot>

        <form wire:submit="deleteSetting" class="space-y-6">
            {{-- Warning --}}
            <div class="p-4 bg-red-50 dark:bg-red-900/20 border-2 border-red-200 dark:border-red-800 rounded-xl">
                <div class="flex gap-3">
                    <div class="p-2 bg-red-100 dark:bg-red-900 rounded-lg">
                        <x-filament::icon icon="heroicon-o-shield-exclamation" class="w-5 h-5 text-red-600 dark:text-red-400" />
                    </div>
                    <div class="text-sm">
                        <p class="font-bold text-red-900 dark:text-red-100 mb-2" style="font-weight: bold">⚠️ {{ __('Warning') }}</p>
                        <ul class="space-y-1.5 text-red-800 dark:text-red-200" >
                            <li class="flex items-start gap-2" style="display: inline-flex; color: red">
                                <x-filament::icon icon="heroicon-m-x-circle" class="w-4 h-4 mt-0.5 flex-shrink-0" />
                                <span>{{ __('The key and all related data will be deleted') }}</span>
                            </li>
                            <li class="flex items-start gap-2" style="display: inline-flex; color: red">
                                <x-filament::icon icon="heroicon-m-x-circle" class="w-4 h-4 mt-0.5 flex-shrink-0" />
                                <span>{{ __('Uploaded files (images/videos) will also be deleted') }}</span>
                            </li>
                            <li class="flex items-start gap-2" style="display: inline-flex; color: red">
                                <x-filament::icon icon="heroicon-m-x-circle" class="w-4 h-4 mt-0.5 flex-shrink-0" />
                                <span>{{ __('Recovery is not possible') }}</span>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>

            {{-- Select --}}
            <div class="space-y-2">
                <label class="flex items-center gap-2 text-sm font-semibold text-gray-700 dark:text-gray-200">
                    <div class="w-8 h-8 flex items-center justify-center rounded-lg bg-red-100 dark:bg-red-900" style="display: inline-flex">
                        <x-filament::icon icon="heroicon-m-cursor-arrow-rays" class="w-4 h-4 text-red-600 dark:text-red-400" />
                    </div>
                    {{ __('Select key to delete') }}
                </label>
                <select
                    wire:model="deleteSettingName"
                    class="block w-full px-4 py-3 rounded-xl border-2 border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 text-gray-900 dark:text-white focus:border-red-500 focus:ring-4 focus:ring-red-500/20"
                >
                    <option value="">{{ __('Select...') }}</option>
                    @php
                        $settings = \App\Models\Setting::query()->where('group', $this->group)->get();
                    @endphp
                    @foreach($settings as $setting)
                        <option value="{{ $setting->name }}">
                            {{ $setting->label }} ({{ $setting->name }})
                        </option>
                    @endforeach
                </select>
                @error('deleteSettingName')
                <div class="flex items-center gap-2 px-3 py-2 bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-lg">
                    <x-filament::icon icon="heroicon-m-exclamation-circle" class="w-4 h-4 text-red-600" />
                    <span class="text-sm text-red-700 dark:text-red-300">{{ $message }}</span>
                </div>
                @enderror
            </div>

            {{-- Actions --}}
            <div class="flex justify-end gap-3 pt-4 border-t border-gray-200 dark:border-gray-700">
                <x-filament::button type="button" color="gray" wire:click="$dispatch('close-modal', { id: 'delete-setting-modal' })">
                    {{ __('Cancel') }}
                </x-filament::button>
                <x-filament::button type="submit" color="danger" icon="heroicon-m-trash">
                    {{ __('Delete Key') }}
                </x-filament::button>
            </div>
        </form>
    </x-filament::modal>

</x-filament-panels::page>
