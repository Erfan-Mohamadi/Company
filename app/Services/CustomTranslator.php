<?php

namespace App\Services;

use Illuminate\Contracts\Translation\Loader;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use App\Models\TranslationKey;
use InvalidArgumentException;

class CustomTranslator extends \Illuminate\Translation\Translator
{
    protected $loader;

    public function __construct(Loader $loader, $locale)
    {
        parent::__construct($loader, $locale);
        $this->loader = $loader;
        $this->setLocale($locale);
    }

    public function get($key, array $replace = [], $locale = null, $fallback = true)
    {
        // First try to get from config files
        $fromParent = $this->getFromConfig($key, $replace, 'en');

        if ($fromParent !== $key) {
            return $fromParent;
        }

        // Then try to get from database
        try {
            $locale = $locale ?: $this->locale;
            $translationKeys = TranslationKey::getKeysAndTranslationsOf($locale);

            if (isset($translationKeys[$key])) {
                $line = $translationKeys[$key];
                return $this->makeReplacements($line, $replace);
            }
        } catch (\Exception $e) {
            // If database is not available, fall back to parent
        }

        return $this->makeReplacements($key, $replace);
    }

    public function getFromConfig($key, array $replace = [], $locale = null, $fallback = true)
    {
        $locale = $locale ?: $this->locale;

        // Load JSON translations
        $this->load('*', '*', $locale);

        $line = $this->loaded['*']['*'][$locale][$key] ?? null;

        if (!isset($line)) {
            [$namespace, $group, $item] = $this->parseKey($key);

            $locales = $fallback ? $this->localeArray($locale) : [$locale];

            foreach ($locales as $locale) {
                if (!is_null($line = $this->getLine($namespace, $group, $locale, $item, $replace))) {
                    return $line;
                }
            }
        }

        return $this->makeReplacements($line ?: $key, $replace);
    }

    public function load($namespace, $group, $locale)
    {
        if ($this->isLoaded($namespace, $group, $locale)) {
            return;
        }

        $lines = $this->loader->load($locale, $group, $namespace);
        $this->loaded[$namespace][$group][$locale] = $lines;
    }

    protected function isLoaded($namespace, $group, $locale)
    {
        return isset($this->loaded[$namespace][$group][$locale]);
    }

    protected function makeReplacements($line, array $replace)
    {
        if (empty($replace) || empty($line)) {
            return $line;
        }

        $replace = $this->sortReplacements($replace);

        foreach ($replace as $key => $value) {
            $line = str_replace(
                [':'.$key, ':'.Str::upper($key), ':'.Str::ucfirst($key)],
                [$value, Str::upper($value), Str::ucfirst($value)],
                $line
            );
        }

        return $line;
    }

    protected function sortReplacements(array $replace)
    {
        return (new Collection($replace))->sortBy(function ($value, $key) {
            return mb_strlen($key) * -1;
        })->all();
    }

    public function choice($key, $number, array $replace = [], $locale = null)
    {
        return $this->get($key, $replace, $locale);
    }

    public function getLocale()
    {
        return $this->locale;
    }

    public function setLocale($locale)
    {
        if (Str::contains($locale, ['/', '\\'])) {
            throw new InvalidArgumentException('Invalid characters present in locale.');
        }

        $this->locale = $locale;
    }
}
