<?php

namespace App\Filament\Widgets;

use App\Models\Accreditation;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Support\Facades\App;

class LatestAccreditations extends BaseWidget
{
    protected int | string | array $columnSpan = 'full';

    protected static ?string $heading = null;

    public static function getHeading(): ?string
    {
        return __('Latest Accreditations');
    }

    public function table(Table $table): Table
    {
        $isFarsi = App::isLocale('fa');

        return $table
            ->recordTitleAttribute('accreditation')
            ->columns([
                TextColumn::make('organization_name')
                    ->label(__('Organization Name'))
                    ->alignCenter(),

                TextColumn::make('membership_number')
                    ->label(__('Membership Number'))
                    ->alignCenter()
                    ->searchable(),

                TextColumn::make('status')
                    ->label(__('Status'))
                    ->alignCenter()
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'draft'     => __('Draft'),
                        'published' => __('Published'),
                        default     => $state,
                    })
                    ->colors([
                        'draft'     => 'gray',
                        'published' => 'success',
                    ]),

                TextColumn::make('start_date')
                    ->label(__('Start Date'))
                    ->sortable()
                    ->alignCenter()
                    ->date($isFarsi ? 'j F Y' : 'M j, Y')
                    ->when(
                        $isFarsi,
                        fn (TextColumn $column) => $column->jalaliDate('j F Y')
                    ),

                TextColumn::make('end_date')
                    ->label(__('End Date'))
                    ->sortable()
                    ->alignCenter()
                    ->date($isFarsi ? 'j F Y' : 'M j, Y')
                    ->when(
                        $isFarsi,
                        fn (TextColumn $column) => $column->jalaliDate('j F Y')
                    ),
            ])
            ->defaultSort('created_at', 'desc')
            ->poll('60s')
            ->paginated(false)
            ->query(Accreditation::query()->latest()->limit(5));
    }
}
