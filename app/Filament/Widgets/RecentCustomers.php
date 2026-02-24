<?php

namespace App\Filament\Widgets;

use App\Models\Customer;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Support\Facades\App;

class RecentCustomers extends BaseWidget
{
    protected int | string | array $columnSpan = 'full';

    protected static ?string $heading = null;

    public static function getHeading(): ?string
    {
        return __('Recent Customers');
    }

    public function table(Table $table): Table
    {
        $isFarsi = App::isLocale('fa');

        return $table
            ->recordTitleAttribute('name')
            ->columns([
                TextColumn::make('name')
                    ->label(__('Name'))
                    ->alignCenter()
                    ->searchable(),

                TextColumn::make('website_url')
                    ->label(__('Website URL'))
                    ->alignCenter()
                    ->url(fn ($record) => $record->website_url)
                    ->openUrlInNewTab(),

                TextColumn::make('status')
                    ->label(__('Status'))
                    ->alignCenter()
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'draft'     => __('Draft'),
                        'published' => __('Published'),
                        'active'    => __('Active'),
                        'inactive'  => __('Inactive'),
                        default     => $state,
                    })
                    ->colors([
                        'draft'     => 'gray',
                        'published' => 'success',
                        'active'    => 'success',
                        'inactive'  => 'danger',
                    ]),

                TextColumn::make('created_at')
                    ->label(__('Created At'))
                    ->sortable()
                    ->alignCenter()
                    ->date($isFarsi ? 'j F Y' : 'M j, Y')
                    ->when(
                        $isFarsi,
                        fn (TextColumn $column) => $column->jalaliDate('j F Y')
                    ),
            ])
            ->defaultSort('id', 'desc')
            ->poll('60s')
            ->paginated(false)
            ->query(Customer::query()->latest()->limit(10));
    }
}
