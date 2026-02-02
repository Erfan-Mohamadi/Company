<?php

namespace App\Filament\Resources\Users\Pages;

use App\Filament\Resources\Users\UserResource;
use App\Models\User;
use Filament\Actions\EditAction;
use Filament\Actions\RestoreAction;
use Filament\Resources\Pages\Concerns\InteractsWithRecord;
use Filament\Resources\Pages\Page;
use Filament\Schemas\Components\EmbeddedTable;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Spatie\Activitylog\Models\Activity;

class ListUserActivities extends Page implements HasTable
{
    use InteractsWithRecord;
    use InteractsWithTable;

    protected static string $resource = UserResource::class;

    protected static ?string $breadcrumb = null;

    public function getBreadcrumb(): string
    {
        return __('Activities');
    }

    public function mount(int | string $record): void
    {
        $this->record = $this->resolveRecord($record);
        $this->mountCanAuthorizeAccess();
    }

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
            RestoreAction::make(),
        ];
    }

    public function getHeading(): string
    {
        return $this->getRecord()->name;
    }

    public function getSubheading(): ?string
    {
        return __('Activities');
    }

    public function getTitle(): string
    {
        return __('Activities') . ' — ' . $this->getRecord()->name;
    }

    public function content(Schema $schema): Schema
    {
        return $schema
            ->components([
                EmbeddedTable::make(),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(function () {
                return Activity::query()->where('subject_type', User::class)
                    ->where('subject_id', $this->getRecord()->id)
                    ->with('causer')
                    ->latest();
            })
            ->columns([
                TextColumn::make('causer.name')
                    ->label(__('Performed By'))
                    ->default(__('System')),

                TextColumn::make('action')
                    ->label(__('Action'))
                    ->getStateUsing(function (Activity $record) {
                        $customMap = [
                            'Roles updated via main form'                => __('Roles Updated'),
                            'Direct permissions updated via main form'   => __('Permissions Updated'),
                            'Initial roles & permissions assigned'       => __('Initial Assignment'),
                            'Roles & Permissions updated'                => __('Roles & Permissions Updated'),
                        ];

                        if (isset($customMap[$record->description])) {
                            return $customMap[$record->description];
                        }

                        $eventMap = [
                            'created' => __('Created'),
                            'updated' => __('Updated'),
                            'deleted' => __('Deleted'),
                        ];

                        $event = $record->getAttribute('event');
                        if ($event && isset($eventMap[$event])) {
                            return $eventMap[$event];
                        }

                        return $record->description ?? __('Unknown action');
                    })
                    ->badge()
                    ->color(function (Activity $record) {
                        $customColors = [
                            'Roles updated via main form'                => 'warning',
                            'Direct permissions updated via main form'   => 'info',
                            'Initial roles & permissions assigned'       => 'success',
                            'Roles & Permissions updated'                => 'warning',
                        ];

                        if (isset($customColors[$record->description])) {
                            return $customColors[$record->description];
                        }

                        $event = $record->getAttribute('event');

                        return match($event) {
                            'created' => 'success',
                            'updated' => 'warning',
                            'deleted' => 'danger',
                            default   => 'gray',
                        };
                    }),

                TextColumn::make('changes')
                    ->label(__('Changes'))
                    ->getStateUsing(function (Activity $record) {
                        $props = $record->properties->toArray();

                        // Custom role/permission log
                        if (array_key_exists('old_roles', $props) || array_key_exists('new_roles', $props)) {
                            $parts = [];

                            $oldRoles = $props['old_roles'] ?? [];
                            $newRoles = $props['new_roles'] ?? [];
                            if ($oldRoles !== $newRoles) {
                                $old = empty($oldRoles) ? __('None') : implode(', ', $oldRoles);
                                $new = empty($newRoles) ? __('None') : implode(', ', $newRoles);
                                $parts[] = __('Roles') . ': ' . $old . ' → ' . $new;
                            }

                            $oldPerms = $props['old_permissions'] ?? [];
                            $newPerms = $props['new_permissions'] ?? [];
                            if ($oldPerms !== $newPerms) {
                                $old = empty($oldPerms) ? __('None') : implode(', ', $oldPerms);
                                $new = empty($newPerms) ? __('None') : implode(', ', $newPerms);
                                $parts[] = __('Permissions') . ': ' . $old . ' → ' . $new;
                            }

                            return implode(' | ', $parts) ?: __('No changes');
                        }

                        // Automatic LogsActivity log
                        if (array_key_exists('attributes', $props)) {
                            $attributes = $props['attributes'] ?? [];
                            $old = $props['old'] ?? [];
                            $parts = [];

                            foreach ($attributes as $field => $newValue) {
                                $oldValue = $old[$field] ?? __('None');
                                if ((string) $oldValue !== (string) $newValue) {
                                    $fieldLabel = __(ucfirst($field));
                                    $parts[] = $fieldLabel . ': ' . $oldValue . ' → ' . $newValue;
                                }
                            }

                            return implode(' | ', $parts) ?: __('No changes');
                        }

                        return __('No details');
                    })
                    ->wrap(),

                TextColumn::make('created_at')
                    ->label(__('Date'))
                    ->jalaliDateTime(),
            ]);
    }
}
