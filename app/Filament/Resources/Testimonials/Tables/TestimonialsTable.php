<?php

namespace App\Filament\Resources\Testimonials\Tables;

use App\Models\Testimonial;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\SpatieMediaLibraryImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Support\Facades\App;

class TestimonialsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                SpatieMediaLibraryImageColumn::make('testimonial_avatars')
                    ->label(__('Photo'))
                    ->collection('testimonial_avatars')
                    ->conversion('thumb')
                    ->circular()
                    ->size(44)
                    ->placeholder(__('—')),

                TextColumn::make('customer_name')
                    ->label(__('Customer'))
                    ->getStateUsing(fn ($record) => $record->getTranslation('customer_name', App::getLocale()) ?? '—')
                    ->searchable()
                    ->limit(35),

                TextColumn::make('customer_company')
                    ->label(__('Company'))
                    ->getStateUsing(fn ($record) => $record->getTranslation('customer_company', App::getLocale()) ?? '—')
                    ->limit(30)
                    ->toggleable(),

                TextColumn::make('rating')
                    ->label(__('Rating'))
                    ->formatStateUsing(fn (int $state): string => str_repeat('⭐', $state))
                    ->alignCenter(),

                IconColumn::make('featured')
                    ->label(__('Featured'))
                    ->boolean()
                    ->trueColor('warning')
                    ->alignCenter(),

                TextColumn::make('status')
                    ->label(__('Status'))
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => Testimonial::getStatuses()[$state] ?? $state)
                    ->color(fn (string $state): string => match ($state) {
                        'active'   => 'success',
                        'pending'  => 'danger',
                        'inactive' => 'gray',
                        default    => 'gray',
                    }),

                TextColumn::make('order')
                    ->label(__('Order'))
                    ->sortable()
                    ->alignCenter()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('order', 'asc')
            ->filters([
                SelectFilter::make('featured')
                    ->options(['1' => __('Featured'), '0' => __('Not Featured')]),

                SelectFilter::make('rating')
                    ->options([1 => '⭐', 2 => '⭐⭐', 3 => '⭐⭐⭐', 4 => '⭐⭐⭐⭐', 5 => '⭐⭐⭐⭐⭐']),

                SelectFilter::make('status')
                    ->options(Testimonial::getStatuses()),
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
