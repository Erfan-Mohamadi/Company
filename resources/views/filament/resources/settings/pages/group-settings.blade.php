@php
    use App\Filament\Resources\Settings\SettingResource;
@endphp

<x-filament-panels::page>

    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
                <x-filament::button tag="a" href="{{ SettingResource::getUrl('index') }}" color="gray">
                    بازگشت
                </x-filament::button>

                <div class="d-flex gap-2">
                    <x-filament::button color="danger" wire:click="$dispatch('open-modal', { id: 'delete-setting-modal' })">
                        حذف کلید
                    </x-filament::button>

                    <x-filament::button color="primary" wire:click="$dispatch('open-modal', { id: 'create-setting-modal' })">
                        ثبت کلید جدید
                    </x-filament::button>
                </div>
            </div>
        </div>
    </div>

    <x-filament::card>
        <x-slot name="heading">ویرایش تنظیمات - {{ $this->group }}</x-slot>

        <form wire:submit="save">
            <div class="row">
                {{ $this->form }}

                <div class="col-12 text-center mt-5">
                    <x-filament::button type="submit" color="warning" size="lg">
                        به روزرسانی
                    </x-filament::button>
                </div>
            </div>
        </form>
    </x-filament::card>

    <!-- Paste your two Bootstrap modals here (delete & create) -->
    <!-- They will work with Livewire wire:submit etc. -->
    <!-- Or better: convert them to Filament modals with ->form([...]) -->

</x-filament-panels::page>
