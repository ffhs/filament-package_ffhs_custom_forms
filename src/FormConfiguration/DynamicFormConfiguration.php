<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\FormConfiguration;

use Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomFieldType;
use Ffhs\FilamentPackageFfhsCustomForms\Models\FormVariation;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

abstract class DynamicFormConfiguration
{

    public abstract static function identifier(): string;
    public abstract static function displayName(): string;

    public  static function formFieldTypes():array{
        return CustomFieldType::getAllTypes();
    }

    public static function displayViewMode():string {
        return self::displayMode();
    }
    public static function displayEditMode():string {
        return self::displayMode();
    }
    public static function displayCreateMode():string {
        return self::displayMode();
    }
    public static function displayMode():string {
        return 'default';
    }

    public static function overwriteViewModes():array {
        return [];
    }


    public static function hasVariations(): bool{
        return false;
    }

    public static function relationVariationsQuery(MorphTo $query): Builder{
        return FormVariation::query()->whereIn("custom_form_id", $query->select("id"));
    }

    public static function variationModel(): ?string{
        return FormVariation::class;
    }


    public static function variationName(Model $variationModel):string {
        return $variationModel->short_title;
    }

    public static function isVariationDisabled(Model $variationModel):bool {
        return $variationModel->is_disabled;
    }
    public static function isVariationHidden(Model $variationModel):bool {
        return $variationModel->is_hidden;
    }

    public final static function getFormConfigurationClass(string $custom_form_identifier):string {
        return collect(config("ffhs_custom_forms.forms"))->where(fn(string $class)=> $class::identifier() == $custom_form_identifier)->first();
    }



}
