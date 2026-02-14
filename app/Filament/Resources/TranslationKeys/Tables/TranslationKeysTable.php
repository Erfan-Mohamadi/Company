<?php

namespace App\Filament\Resources\TranslationKeys\Tables;

use App\Models\Language;
use App\Models\TranslationKey;
use Filament\Actions\Action;
use Filament\Actions\BulkAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;

class TranslationKeysTable
{
    public static function configure(Table $table): Table
    {
        $languages = Language::getAllLanguages();

        return $table
            ->columns([
                TextColumn::make('key')
                    ->label(__('Key'))
                    ->searchable()
                    ->sortable()
                    ->copyable()
                    ->copyMessage(__('Key copied!'))
                    ->weight('bold'),

                TextColumn::make('group')
                    ->label(__('Group'))
                    ->searchable()
                    ->sortable()
                    ->badge()
                    ->color('info')
                    ->default('Ungrouped'),

                ...collect($languages)->map(function ($language) {
                    return TextColumn::make("value.{$language->name}")
                        ->label(__($language->label))
                        ->limit(50)
                        ->tooltip(function ($record) use ($language) {
                            return $record->getTranslation('value', $language->name);
                        })
                        ->toggleable(isToggledHiddenByDefault: $language->name !== Language::MAIN_LANG)
                        ->searchable()
                        ->badge()
                        ->color(fn ($state) => empty($state) ? 'danger' : 'success');
                })->toArray(),

                IconColumn::make('message')
                    ->label(__('Message'))
                    ->boolean()
                    ->trueIcon('heroicon-o-chat-bubble-left-right')
                    ->falseIcon('heroicon-o-code-bracket')
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('updated_at')
                    ->label(__('Last Updated'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('group')
                    ->label(__('Group'))
                    ->options(function () {
                        return TranslationKey::query()
                            ->whereNotNull('group')
                            ->distinct()
                            ->pluck('group', 'group')
                            ->toArray();
                    })
                    ->multiple(),

                TernaryFilter::make('message')
                    ->label(__('Type'))
                    ->placeholder('All')
                    ->trueLabel('Messages Only')
                    ->falseLabel('Code Keys Only'),

                SelectFilter::make('missing_translations')
                    ->label(__('Missing Translations'))
                    ->options(function () {
                        $languages = Language::pluck('label', 'name')->toArray();
                        return $languages;
                    })
                    ->query(function ($query, $data) {
                        if (!empty($data['value'])) {
                            $language = $data['value'];
                            return $query->whereRaw("JSON_EXTRACT(value, '$.{$language}') IS NULL OR JSON_EXTRACT(value, '$.{$language}') = ''");
                        }
                        return $query;
                    }),
            ])
            ->actions([
                ViewAction::make(),
                EditAction::make(),
                DeleteAction::make()
                    ->after(function () {
                        TranslationKey::clearCaches();
                    }),
                Action::make('duplicate')
                    ->label(__('Duplicate'))
                    ->icon('heroicon-o-document-duplicate')
                    ->color('warning')
                    ->form([
                        TextInput::make('new_key')
                            ->label(__('New Key'))
                            ->required()
                            ->unique('lang_website_keys', 'key')
                            ->placeholder(__('Enter new key name')),
                    ])
                    ->action(function (TranslationKey $record, array $data) {
                        TranslationKey::create([
                            'key' => $data['new_key'],
                            'value' => $record->value,
                            'group' => $record->group,
                            'message' => $record->message,
                        ]);

                        Notification::make()
                            ->title('Translation Key Duplicated')
                            ->success()
                            ->send();
                    }),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()
                        ->after(function () {
                            TranslationKey::clearCaches();
                        }),
                    BulkAction::make('change_group')
                        ->label(__('Change Group'))
                        ->icon('heroicon-o-folder')
                        ->form([
                            TextInput::make('group')
                                ->label(__('New Group'))
                                ->required(),
                        ])
                        ->action(function ($records, array $data) {
                            $records->each->update(['group' => $data['group']]);
                            TranslationKey::clearCaches();

                            Notification::make()
                                ->title('Group Updated')
                                ->success()
                                ->send();
                        }),
                ]),
            ])
            ->defaultSort('key', 'asc');
    }
}
