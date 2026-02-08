@php
    use App\Filament\Resources\Settings\SettingResource;

    // Map color names to actual gradient colors
    $colorGradients = [
        'primary' => 'linear-gradient(135deg, #6366f1 0%, #4f46e5 100%)',
        'success' => 'linear-gradient(135deg, #10b981 0%, #059669 100%)',
        'warning' => 'linear-gradient(135deg, #f59e0b 0%, #d97706 100%)',
        'danger' => 'linear-gradient(135deg, #ef4444 0%, #dc2626 100%)',
        'pink' => 'linear-gradient(135deg, #ec4899 0%, #db2777 100%)',
        'info' => 'linear-gradient(135deg, #3b82f6 0%, #2563eb 100%)',
        'gray' => 'linear-gradient(135deg, #6b7280 0%, #4b5563 100%)',
    ];
@endphp

<x-filament-panels::page>
    <style>
        /* Explicit 4-column layout */
        .settings-container {
            display: grid;
            grid-template-columns: 1fr 1fr 1fr 1fr;
            gap: 24px;
            width: 100%;
        }

        /* Tablet - 2 columns */
        @media (max-width: 1024px) {
            .settings-container {
                grid-template-columns: 1fr 1fr;
            }
        }

        /* Mobile - 1 column */
        @media (max-width: 640px) {
            .settings-container {
                grid-template-columns: 1fr;
            }
        }

        .setting-card {
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
            transition: all 0.3s;
            display: flex;
            flex-direction: column;
        }

        .setting-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 12px 24px -10px rgba(0, 0, 0, 0.15);
        }

        .card-content {
            padding: 2rem;
            display: flex;
            flex-direction: column;
            align-items: center;
            text-align: center;
            flex: 1;
            color: white;
        }

        .icon-wrapper {
            margin-bottom: 1.5rem;
        }

        .card-title {
            font-size: 1.25rem;
            font-weight: 700;
            margin-bottom: 0.75rem;
            text-align: center;
        }

        .card-description {
            font-size: 0.875rem;
            opacity: 0.9;
            margin-bottom: 1.5rem;
            flex: 1;
            min-height: 60px;
            text-align: center;
            line-height: 1.5;
        }

        .edit-button {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 0.625rem 1.5rem;
            background-color: white;
            color: #374151;
            font-weight: 500;
            border-radius: 9999px;
            transition: all 0.2s;
            text-decoration: none;
            box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1);
        }

        .edit-button:hover {
            background-color: #f9fafb;
            transform: scale(1.02);
        }
    </style>

    <div class="settings-container">
        @foreach($this->groups as $groupKey => $options)
            @php
                $gradient = $colorGradients[$options['bg']] ?? $colorGradients['primary'];
            @endphp

            <div class="setting-card" style="background: {{ $gradient }};">
                <div class="card-content">

                    {{-- Icon --}}
                    <div class="icon-wrapper">
                        <x-filament::icon
                            :icon="'heroicon-o-' . $options['icon']"
                            style="width: 64px; height: 64px; color: white; filter: drop-shadow(0 4px 6px rgba(0, 0, 0, 0.1));"
                        />
                    </div>

                    {{-- Title --}}
                    <h3 class="card-title">
                        {{ __($options['title']) }}
                    </h3>

                    {{-- Description --}}
                    <p class="card-description">
                        {{ __($options['summary']) }}
                    </p>

                    {{-- Edit button --}}
                    <a href="{{ SettingResource::getUrl('group', ['group' => $groupKey]) }}" class="edit-button">
                        {{ __('Edit') }}
                    </a>
                </div>
            </div>
        @endforeach
    </div>
</x-filament-panels::page>
