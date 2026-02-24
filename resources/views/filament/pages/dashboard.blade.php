<x-filament-panels::page>

    <div class="space-y-8 pb-8">

        <!-- Header / Welcome Section -->
        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h2 class="text-2xl font-bold tracking-tight text-gray-900 dark:text-white">
                    Welcome back, {{ auth()->user()->name ?? 'Admin' }}
                </h2>
                <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                    Here's what's happening with your company today • {{ now()->format('F j, Y') }}
                </p>
            </div>

            <!-- Quick Actions -->
            <div class="flex flex-wrap gap-3">
                <x-filament::button
                    icon="heroicon-o-plus"
                    href="{{ route('filament.admin.resources.accreditations.create') }}"
                    color="primary"
                >
                    New Accreditation
                </x-filament::button>

                <x-filament::button
                    icon="heroicon-o-user-plus"
                    href="{{ route('filament.admin.resources.customers.create') }}"
                    outlined
                >
                    Add Customer
                </x-filament::button>

                <!-- Add more quick actions as needed -->
            </div>
        </div>

        <!-- Stats Overview Widget (usually spans full width or large columns) -->
        <x-filament-widgets::widgets
            :widgets="$this->getVisibleWidgets()"
            :columns="$this->getColumns()"
        />

        <!-- Chart + Tables section -->
        <div class="grid grid-cols-1 gap-6 lg:grid-cols-2 xl:grid-cols-3">

            <!-- Accreditation Status Chart (can span 1 or 2 columns depending on layout) -->
            <div class="col-span-1 lg:col-span-2 xl:col-span-1">
                <x-filament::card>
                    <x-filament-widgets::widget
                        :widget="\App\Filament\Widgets\AccreditationStatusChart::class"
                    />
                </x-filament::card>
            </div>

            <!-- Recent Customers Table -->
            <div class="col-span-1">
                <x-filament::card>
                    <x-filament-widgets::widget
                        :widget="\App\Filament\Widgets\RecentCustomers::class"
                    />
                </x-filament::card>
            </div>

            <!-- Latest Accreditations Table -->
            <div class="col-span-1">
                <x-filament::card>
                    <x-filament-widgets::widget
                        :widget="\App\Filament\Widgets\LatestAccreditations::class"
                    />
                </x-filament::card>
            </div>

        </div>

    </div>

</x-filament-panels::page>
