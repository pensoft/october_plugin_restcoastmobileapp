<?php

namespace Pensoft\Restcoast\Services;

use Illuminate\Database\Eloquent\Model;
use RainLab\Translate\Models\Locale;

class TranslationService {
    public function getAllWithTranslations( Model $model ): array {
        $locales = Locale::all();

        $returnedArray = [ $model::all() ];
        foreach ( $locales as $locale ) {
            $returnedArray[ $locale->code ] = $model::transWhere( 'locale', $locale->code );
        }

        return $returnedArray;
    }

    public function getOneWithTranslations( Model $model ): array {
        $locales = Locale::all();

        $returnedArray = [ $model ];
        foreach ( $locales as $locale ) {
            $model->translateContext( $locale->code );
            $returnedArray[ $locale->code ] = $model;
        }

        return $returnedArray;
    }
}
