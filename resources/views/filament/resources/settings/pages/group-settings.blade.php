@php
    use App\Filament\Resources\Settings\SettingResource;
    use App\Models\Setting;
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

    <!-- Create Setting Modal -->
    <x-filament::modal id="create-setting-modal" displayClasses="block" alignment="center">
        <x-slot name="heading">
            ثبت کلید جدید
        </x-slot>

        <form wire:submit="createSetting" class="space-y-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">نام کلید (انگلیسی)</label>
                <input
                    type="text"
                    wire:model="newSetting.name"
                    placeholder="site_title, alo_video"
                    class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500"
                    required
                />
                @error('newSetting.name')
                <span class="text-red-600 text-sm mt-1 block">{{ $message }}</span>
                @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">برچسب (فارسی)</label>
                <input
                    type="text"
                    wire:model="newSetting.label"
                    placeholder="عنوان سایت"
                    class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500"
                    required
                />
                @error('newSetting.label')
                <span class="text-red-600 text-sm mt-1 block">{{ $message }}</span>
                @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">نوع</label>
                <select
                    wire:model="newSetting.type"
                    class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500"
                    required
                >
                    <option value="">انتخاب کنید</option>
                    @foreach(Setting::getAllTypes() as $key => $label)
                        <option value="{{ $key }}">{{ $label }}</option>
                    @endforeach
                </select>
                @error('newSetting.type')
                <span class="text-red-600 text-sm mt-1 block">{{ $message }}</span>
                @enderror
            </div>

            <div class="flex gap-3 justify-end pt-4 border-t border-gray-200">
                <x-filament::button
                    type="button"
                    color="gray"
                    wire:click="$dispatch('close-modal', { id: 'create-setting-modal' })"
                >
                    انصراف
                </x-filament::button>
                <x-filament::button type="submit" color="primary">
                    ثبت کلید
                </x-filament::button>
            </div>
        </form>
    </x-filament::modal>

    <!-- Delete Setting Modal -->
    <x-filament::modal id="delete-setting-modal" displayClasses="block" alignment="center">
        <x-slot name="heading">
            حذف کلید
        </x-slot>

        <form wire:submit="deleteSetting" class="space-y-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">انتخاب کلید برای حذف</label>
                <select
                    wire:model="deleteSettingName"
                    class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-red-500 focus:border-red-500"
                    required
                >
                    <option value="">انتخاب کنید</option>
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
                <span class="text-red-600 text-sm mt-1 block">{{ $message }}</span>
                @enderror
            </div>

            <div class="bg-red-50 border border-red-200 rounded-md p-3 mb-4">
                <p class="text-sm text-red-800">
                    <strong>تنبیه:</strong> حذف این کلید برگشت‌ناپذیر است و تمام داده‌های مرتبط حذف خواهند شد.
                </p>
            </div>

            <div class="flex gap-3 justify-end pt-4 border-t border-gray-200">
                <x-filament::button
                    type="button"
                    color="gray"
                    wire:click="$dispatch('close-modal', { id: 'delete-setting-modal' })"
                >
                    انصراف
                </x-filament::button>
                <x-filament::button type="submit" color="danger">
                    حذف کلید
                </x-filament::button>
            </div>
        </form>
    </x-filament::modal>

</x-filament-panels::page>
