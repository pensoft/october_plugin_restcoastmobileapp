<?php

namespace Pensoft\RestcoastMobileApp\Services;

use Illuminate\Database\Eloquent\Model;
use RainLab\Translate\Models\Locale;

class TranslationService
{
    public function getAllWithTranslations($model): array
    {
        $locales = Locale::listEnabled();

        $records = $model::all();
        foreach ($locales as $localeCode => $locale) {
            foreach ($records as $record) {
                $record->translateContext($localeCode);
                $returnedArray[$localeCode][] = $record;
            }
        }

        return $returnedArray;
    }

    public function getOneWithTranslations(Model $model): array
    {
        $locales = Locale::listEnabled();

        foreach ($locales as $localeCode => $locale) {
            $model->translateContext($localeCode);
            $returnedArray[$localeCode] = $model;
        }

        return $returnedArray;
    }
}
