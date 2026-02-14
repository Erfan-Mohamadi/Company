<?php

namespace App\Rules;

use App\Models\Language;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class AvailableLocale implements ValidationRule
{
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        // Extract language code from attribute name
        // e.g., "languages.fa" -> "fa"
        $parts = explode('.', $attribute);
        $language = end($parts);

        if (!Language::languageExists($language)) {
            $fail("The selected language '{$language}' is not available.");
        }
    }
}
