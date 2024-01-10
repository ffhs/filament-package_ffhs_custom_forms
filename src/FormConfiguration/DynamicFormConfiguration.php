<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\FormConfiguration;

use Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomFieldType;
use Ffhs\FilamentPackageFfhsCustomForms\Models\FormVariation;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use PhpParser\Node\Expr\AssignOp\Mod;

abstract class DynamicFormConfiguration
{

    public abstract static function identifier(): string;
    public abstract static function displayName(): string;

    public  static function formFieldTypes():array{
        return CustomFieldType::getAllTypes();
    }

    public static function displayViewMode():string {
        return 'default';
    }
    public static function displayEditMode():string {
        return 'default';
    }
    public static function displayCreateMode():string {
        return 'default';
    }
    public static function displayMode():string {
        return 'default';
    }

    public static function getOverwriteViewModes():array {
        return [];
    }


    public static function hasVariations(): bool{
        return false;
    }

    public static function relationVariationsQuery(MorphTo $query): Builder{
        return FormVariation::query()->whereIn("custom_form_id", fn() => $query->select("id"));
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



    public final static function getFormConfigurationClass(string $custom_form_identifier):string {
        return collect(config("ffhs_custom_forms.forms"))->where(fn(string $class)=> $class::identifier() == $custom_form_identifier)->first();
    }



}
