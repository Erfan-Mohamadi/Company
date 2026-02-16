<?php

namespace App\Filament\Resources\TranslationKeys\Tables;

use App\Models\Language;
use App\Models\TranslationKey;
use Filament\Actions\Action;
use Filament\Actions\BulkAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Notifications\Notification;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;
use Filament\Forms\Components\TextInput;
use Illuminate\Support\Facades\App;

class TranslationKeysTable
{
    public static function configure(Table $table): Table
    {
        $languages = Language::getAllLanguages();
        $isFarsi = App::isLocale('fa');

        return $table
            ->columns([
                TextColumn::make('key')
                    ->label(__('Key'))
                    ->searchable()
                    ->sortable()
                    ->copyable()
                    ->weight('bold'),

                TextColumn::make('group')
                    ->label(__('Group'))
                    ->searchable()
                    ->sortable()
                    ->badge()
                    ->color('info')
                    ->default('Ungrouped'),

                // Dynamic translation columns
                ...$languages->map(fn ($lang) =>
                TextColumn::make("value_{$lang->name}")
                    ->label($lang->label)
                    ->limit(60)
                    ->wrap()
                    ->getStateUsing(fn ($record) => $record->getTranslation('value', $lang->name, false))
                    ->tooltip(fn ($record) => $record->getTranslation('value', $lang->name, false) ?: '—')
                    ->toggleable(isToggledHiddenByDefault: $lang->name !== Language::MAIN_LANG)
                    ->searchable(query: fn ($q, $search) =>
                    $q->whereJsonContains("value->{$lang->name}", $search)
                    )
                    ->badge()
                    ->color(fn ($state) => filled($state) ? 'success' : 'danger')
                    ->default('—')
                )->toArray(),

                IconColumn::make('message')
                    ->label(__('Type'))
                    ->boolean()
                    ->trueIcon('heroicon-o-chat-bubble-left-right')
                    ->falseIcon('heroicon-o-code-bracket')
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('updated_at')
                    ->label(__('Last Updated'))
                    ->date($isFarsi ? 'j F Y' : 'F j, Y')
                    ->when(
                        $isFarsi,
                        fn (TextColumn $column) => $column->jalaliDate('j F Y')
                    )
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('group')
                    ->label(__('Group'))
                    ->multiple()
                    ->options(
                        TranslationKey::query()
                            ->whereNotNull('group')
                            ->distinct()
                            ->pluck('group', 'group')
                            ->toArray()
                    ),

                TernaryFilter::make('message')
                    ->label(__('Type'))
                    ->placeholder(__('All'))
                    ->trueLabel(__('Messages only'))
                    ->falseLabel(__('Code keys only')),

                SelectFilter::make('missing_in')
                    ->label(__('Missing translation in'))
                    ->options(
                        Language::getAllLanguages()->pluck('label', 'name')->toArray()
                    )
                    ->query(function ($query, array $data) {
                        if (!$data['value']) return $query;
                        $lang = $data['value'];
                        return $query->where(function ($q) use ($lang) {
                            $q->whereNull("value->{$lang}")
                                ->orWhere("value->{$lang}", '');
                        });
                    }),
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
                Action::make('duplicate')
                    ->label(__('Duplicate'))
                    ->icon('heroicon-o-document-duplicate')
                    ->color('warning')
                    ->form([
                        TextInput::make('new_key')
                            ->required()
                            ->unique('lang_website_keys', 'key')
                            ->placeholder('Enter new key name'),
                    ])
                    ->action(function (TranslationKey $record, array $data) {
                        TranslationKey::create([
                            'key'     => $data['new_key'],
                            'value'   => $record->value,
                            'group'   => $record->group,
                            'message' => $record->message,
                        ]);
                        Notification::make()->success()->title('Key duplicated')->send();
                    }),
            ])
            ->toolbarActions([
                DeleteBulkAction::make()
                    ->after(fn () => TranslationKey::clearCaches()),
                BulkAction::make('change_group')
                    ->label(__('Change group'))
                    ->icon('heroicon-o-folder')
                    ->form([
                        TextInput::make('group')->required(),
                    ])
                    ->action(function ($records, array $data) {
                        $records->each->update(['group' => $data['group']]);
                        TranslationKey::clearCaches();
                        Notification::make()->success()->title('Group updated')->send();
                    }),
            ])
            ->defaultSort('key', 'asc');
    }
}
