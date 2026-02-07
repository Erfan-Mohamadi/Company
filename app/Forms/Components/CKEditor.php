<?php

namespace App\Forms\Components;

use Filament\Forms\Components\Field;

class CKEditor extends Field
{
    protected string $view = 'forms.components.ckeditor-field';

    protected function setUp(): void
    {
        parent::setUp();
        $this->dehydrateStateUsing(fn ($state) => $state);
    }
}
